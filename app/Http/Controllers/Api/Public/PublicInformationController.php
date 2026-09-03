<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicInformationResource;
use App\Models\PublicInformation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicInformationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $informations = PublicInformation::query()
            ->with(['category', 'workUnit'])
            ->where('status', 'published')
            ->when($request->string('category')->isNotEmpty(), fn ($query) => $query->whereHas(
                'category',
                fn ($q) => $q->where('slug', $request->string('category')),
            ))
            ->when($request->integer('work_unit_id'), fn ($query, $workUnitId) => $query->where('work_unit_id', $workUnitId))
            ->when($request->integer('year'), fn ($query, $year) => $query->whereYear('published_at', $year))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where(
                fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%')
                    ->orWhere('description', 'like', '%'.$request->string('q').'%'),
            ))
            ->latest('published_at')
            ->paginate(6);

        return PublicInformationResource::collection($informations);
    }

    public function show(PublicInformation $information): PublicInformationResource
    {
        abort_unless($information->status === 'published', 404);

        $information->load(['category', 'workUnit']);
        $information->increment('view_count');

        return new PublicInformationResource($information);
    }

    public function byType(string $type): AnonymousResourceCollection
    {
        abort_unless(in_array($type, ['berkala', 'serta_merta', 'setiap_saat', 'dikecualikan'], true), 404);

        $informations = PublicInformation::query()
            ->with(['category', 'workUnit'])
            ->where('status', 'published')
            ->whereHas('category', fn ($q) => $q->where('type', $type))
            ->latest('published_at')
            ->paginate(6);

        return PublicInformationResource::collection($informations);
    }
}
