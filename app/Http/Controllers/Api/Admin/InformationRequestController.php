<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RespondRequestRequest;
use App\Http\Requests\Admin\UpdateRequestStatusRequest;
use App\Http\Resources\InformationRequestResource;
use App\Models\InformationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InformationRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $requests = InformationRequest::query()
            ->with(['user', 'assignedTo'])
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where(
                'request_number', 'like', '%'.$request->string('q').'%',
            ))
            ->latest()
            ->paginate(20);

        return InformationRequestResource::collection($requests);
    }

    public function show(InformationRequest $informationRequest): InformationRequestResource
    {
        $informationRequest->load(['user', 'assignedTo', 'responses', 'objections', 'logs']);

        return new InformationRequestResource($informationRequest);
    }

    public function updateStatus(
        UpdateRequestStatusRequest $request,
        InformationRequest $informationRequest,
    ): InformationRequestResource {
        $oldStatus = $informationRequest->status;

        $informationRequest->update([
            'status' => $request->validated('status'),
            'rejection_reason' => $request->validated('rejection_reason'),
            'assigned_to' => $request->validated('assigned_to') ?? $informationRequest->assigned_to,
        ]);

        $informationRequest->logs()->create([
            'user_id' => $request->user()->id,
            'action' => 'status_changed',
            'description' => "Status diubah oleh {$request->user()->name}.",
            'old_status' => $oldStatus,
            'new_status' => $informationRequest->status,
        ]);

        return new InformationRequestResource($informationRequest->load(['user', 'assignedTo', 'responses', 'objections', 'logs']));
    }

    public function respond(
        RespondRequestRequest $request,
        InformationRequest $informationRequest,
    ): InformationRequestResource {
        $response = $informationRequest->responses()->create([
            'admin_id' => $request->user()->id,
            'response_text' => $request->validated('response_text'),
            'responded_at' => now(),
        ]);

        if ($request->hasFile('file')) {
            $response->addMediaFromRequest('file')->toMediaCollection('response');
        }

        $oldStatus = $informationRequest->status;
        $informationRequest->update(['status' => 'answered']);

        $informationRequest->logs()->create([
            'user_id' => $request->user()->id,
            'action' => 'answered',
            'description' => "Jawaban dikirim oleh {$request->user()->name}.",
            'old_status' => $oldStatus,
            'new_status' => 'answered',
        ]);

        return new InformationRequestResource($informationRequest->fresh(['user', 'assignedTo', 'responses', 'objections', 'logs']));
    }
}
