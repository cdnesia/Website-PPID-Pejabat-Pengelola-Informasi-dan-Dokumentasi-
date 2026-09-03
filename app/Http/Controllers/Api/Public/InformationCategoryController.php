<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\InformationCategoryResource;
use App\Models\InformationCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InformationCategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = InformationCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return InformationCategoryResource::collection($categories);
    }
}
