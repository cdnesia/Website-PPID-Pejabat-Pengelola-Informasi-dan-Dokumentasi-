<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequestRequest;
use App\Http\Resources\InformationRequestResource;
use App\Models\InformationRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InformationRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $requests = $request->user()
            ->informationRequests()
            ->with(['responses', 'objections'])
            ->latest()
            ->paginate(15);

        return InformationRequestResource::collection($requests);
    }

    public function store(StoreInformationRequestRequest $request): InformationRequestResource
    {
        $informationRequest = InformationRequest::create([
            ...$request->validated(),
            'user_id' => $request->user()?->id,
            'status' => 'submitted',
            'due_date' => now()->addWeekdays(Setting::current()->response_deadline_days),
        ]);

        if ($request->hasFile('ktp')) {
            $informationRequest->addMediaFromRequest('ktp')->toMediaCollection('ktp');
        }

        if ($request->hasFile('power_of_attorney')) {
            $informationRequest->addMediaFromRequest('power_of_attorney')->toMediaCollection('power_of_attorney');
        }

        $informationRequest->logs()->create([
            'user_id' => $request->user()?->id,
            'action' => 'submitted',
            'description' => 'Permohonan diajukan oleh pemohon.',
            'new_status' => 'submitted',
        ]);

        return new InformationRequestResource($informationRequest);
    }

    public function show(Request $request, InformationRequest $informationRequest): InformationRequestResource
    {
        abort_unless($informationRequest->user_id === $request->user()->id, 403);

        $informationRequest->load(['responses', 'objections', 'logs']);

        return new InformationRequestResource($informationRequest);
    }

    public function track(string $requestNumber): InformationRequestResource
    {
        $informationRequest = InformationRequest::query()
            ->where('request_number', $requestNumber)
            ->with(['responses'])
            ->firstOrFail();

        return new InformationRequestResource($informationRequest);
    }
}
