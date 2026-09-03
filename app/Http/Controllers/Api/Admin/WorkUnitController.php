<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkUnitRequest;
use App\Http\Requests\Admin\UpdateWorkUnitRequest;
use App\Http\Resources\WorkUnitResource;
use App\Models\WorkUnit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorkUnitController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return WorkUnitResource::collection(WorkUnit::query()->latest()->paginate(20));
    }

    public function store(StoreWorkUnitRequest $request): WorkUnitResource
    {
        return new WorkUnitResource(WorkUnit::create($request->validated())->refresh());
    }

    public function update(UpdateWorkUnitRequest $request, WorkUnit $workUnit): WorkUnitResource
    {
        $workUnit->update($request->validated());

        return new WorkUnitResource($workUnit);
    }

    public function destroy(WorkUnit $workUnit): Response
    {
        $workUnit->delete();

        return response()->noContent();
    }
}
