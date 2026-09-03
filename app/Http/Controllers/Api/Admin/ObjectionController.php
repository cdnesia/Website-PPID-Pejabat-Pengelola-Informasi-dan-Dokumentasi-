<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RespondObjectionRequest;
use App\Http\Resources\ObjectionResource;
use App\Models\Objection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ObjectionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $objections = Objection::query()
            ->with(['user', 'request'])
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(20);

        return ObjectionResource::collection($objections);
    }

    public function show(Objection $objection): ObjectionResource
    {
        return new ObjectionResource($objection->load(['user', 'request']));
    }

    public function respond(RespondObjectionRequest $request, Objection $objection): ObjectionResource
    {
        $objection->update([
            'status' => $request->validated('status'),
            'response_text' => $request->validated('response_text'),
            'responded_at' => now(),
        ]);

        return new ObjectionResource($objection);
    }
}
