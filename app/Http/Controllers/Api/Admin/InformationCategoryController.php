<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInformationCategoryRequest;
use App\Http\Requests\Admin\UpdateInformationCategoryRequest;
use App\Http\Resources\InformationCategoryResource;
use App\Models\InformationCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class InformationCategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return InformationCategoryResource::collection(InformationCategory::query()->latest()->paginate(20));
    }

    public function store(StoreInformationCategoryRequest $request): InformationCategoryResource
    {
        $category = InformationCategory::create([
            ...$request->validated(),
            'slug' => Str::slug($request->validated('name')).'-'.Str::random(6),
        ]);

        return new InformationCategoryResource($category->refresh());
    }

    public function update(UpdateInformationCategoryRequest $request, InformationCategory $category): InformationCategoryResource
    {
        $category->update($request->validated());

        return new InformationCategoryResource($category);
    }

    public function destroy(InformationCategory $category): Response
    {
        $category->delete();

        return response()->noContent();
    }
}
