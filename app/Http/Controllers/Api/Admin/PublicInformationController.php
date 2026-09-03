<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePublicInformationRequest;
use App\Http\Requests\Admin\UpdatePublicInformationRequest;
use App\Http\Resources\PublicInformationResource;
use App\Models\PublicInformation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PublicInformationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $informations = PublicInformation::query()
            ->with(['category', 'workUnit'])
            ->latest()
            ->paginate(20);

        return PublicInformationResource::collection($informations);
    }

    public function show(PublicInformation $information): PublicInformationResource
    {
        return new PublicInformationResource($information->load(['category', 'workUnit']));
    }

    public function store(StorePublicInformationRequest $request): PublicInformationResource
    {
        $information = PublicInformation::create([
            ...$request->safe()->all(),
            'slug' => Str::slug($request->validated('title')).'-'.Str::random(6),
            'created_by' => $request->user()->id,
        ]);

        return new PublicInformationResource($information->load(['category', 'workUnit']));
    }

    public function update(
        UpdatePublicInformationRequest $request,
        PublicInformation $information,
    ): PublicInformationResource {
        $information->update($request->safe()->all());

        return new PublicInformationResource($information->load(['category', 'workUnit']));
    }

    public function destroy(PublicInformation $information): Response
    {
        $information->delete();

        return response()->noContent();
    }
}
