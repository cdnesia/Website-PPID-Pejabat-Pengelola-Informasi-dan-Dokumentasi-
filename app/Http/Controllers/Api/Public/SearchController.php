<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Resources\PublicInformationResource;
use App\Models\News;
use App\Models\PublicInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function index(Request $request): array
    {
        Validator::make($request->all(), [
            'q' => ['required', 'string', 'min:2'],
        ])->validate();

        $query = $request->string('q');

        $informations = PublicInformation::query()
            ->with(['category', 'workUnit'])
            ->where('status', 'published')
            ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%"))
            ->limit(10)
            ->get();

        $news = News::query()
            ->where('is_published', true)
            ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")
                ->orWhere('excerpt', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%"))
            ->limit(10)
            ->get();

        return [
            'informations' => PublicInformationResource::collection($informations),
            'news' => NewsResource::collection($news),
        ];
    }
}
