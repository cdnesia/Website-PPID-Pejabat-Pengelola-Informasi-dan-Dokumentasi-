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
            ...$this->withPublishedAt($request->safe()->all()),
            'slug' => Str::slug($request->validated('title')).'-'.Str::random(6),
            'created_by' => $request->user()->id,
        ]);

        return new PublicInformationResource($information->load(['category', 'workUnit']));
    }

    public function update(
        UpdatePublicInformationRequest $request,
        PublicInformation $information,
    ): PublicInformationResource {
        $information->update($this->withPublishedAt($request->safe()->all(), $information));

        return new PublicInformationResource($information->load(['category', 'workUnit']));
    }

    /**
     * Keep `published_at` in sync with the `status` field when the request
     * does not explicitly set a publish date.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withPublishedAt(array $data, ?PublicInformation $information = null): array
    {
        if (array_key_exists('published_at', $data) && $data['published_at']) {
            return $data;
        }

        $status = $data['status'] ?? $information?->status;

        if ($status === 'published') {
            $data['published_at'] = $information?->published_at ?? now();
        } elseif ($status === 'draft') {
            $data['published_at'] = null;
        }

        return $data;
    }

    public function destroy(PublicInformation $information): Response
    {
        $information->delete();

        return response()->noContent();
    }
}
