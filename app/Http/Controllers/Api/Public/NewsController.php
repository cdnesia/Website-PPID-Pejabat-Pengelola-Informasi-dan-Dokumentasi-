<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $news = News::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate(6);

        return NewsResource::collection($news);
    }

    public function show(News $news): NewsResource
    {
        abort_unless($news->is_published, 404);

        $news->load('author');
        $news->increment('view_count');

        return new NewsResource($news);
    }
}
