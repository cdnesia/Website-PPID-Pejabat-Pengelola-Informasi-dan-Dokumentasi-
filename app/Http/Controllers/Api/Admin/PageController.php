<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(Page::query()->orderBy('title')->get());
    }

    public function show(Page $page): PageResource
    {
        return new PageResource($page);
    }

    public function update(UpdatePageRequest $request, Page $page): PageResource
    {
        $page->update($request->safe()->except('image'));

        if ($request->hasFile('image')) {
            $page->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return new PageResource($page->refresh());
    }
}
