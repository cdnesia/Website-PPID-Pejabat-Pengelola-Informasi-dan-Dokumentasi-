<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Http\Requests\Admin\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return NewsResource::collection(News::query()->with('author')->latest()->paginate(20));
    }

    public function show(News $news): NewsResource
    {
        return new NewsResource($news->load('author'));
    }

    public function store(StoreNewsRequest $request): NewsResource
    {
        $news = News::create([
            ...$this->withPublishedAt($request->safe()->except('thumbnail')),
            'slug' => Str::slug($request->validated('title')).'-'.Str::random(6),
            'author_id' => $request->user()->id,
        ]);

        if ($request->hasFile('thumbnail')) {
            $news->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return new NewsResource($news->refresh()->load('author'));
    }

    public function update(UpdateNewsRequest $request, News $news): NewsResource
    {
        $news->update($this->withPublishedAt($request->safe()->except('thumbnail'), $news));

        if ($request->hasFile('thumbnail')) {
            $news->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return new NewsResource($news->load('author'));
    }

    /**
     * Keep `published_at` in sync with the `is_published` flag when the
     * request does not explicitly set a publish date.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withPublishedAt(array $data, ?News $news = null): array
    {
        if (array_key_exists('published_at', $data) && $data['published_at']) {
            return $data;
        }

        if ($data['is_published'] ?? false) {
            $data['published_at'] = $news?->published_at ?? now();
        } else {
            $data['published_at'] = null;
        }

        return $data;
    }

    public function destroy(News $news): Response
    {
        $news->delete();

        return response()->noContent();
    }
}
