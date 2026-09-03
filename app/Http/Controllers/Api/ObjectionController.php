<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObjectionRequest;
use App\Http\Resources\ObjectionResource;
use App\Models\InformationRequest;

class ObjectionController extends Controller
{
    public function store(
        StoreObjectionRequest $request,
        InformationRequest $informationRequest,
    ): ObjectionResource {
        abort_unless($informationRequest->user_id === $request->user()->id, 403);

        $objection = $informationRequest->objections()->create([
            'user_id' => $request->user()->id,
            'reason' => $request->validated('reason'),
            'status' => 'submitted',
        ]);

        if ($request->hasFile('evidence')) {
            $objection->addMediaFromRequest('evidence')->toMediaCollection('evidence');
        }

        return new ObjectionResource($objection);
    }
}
