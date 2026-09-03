<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkUnitResource;
use App\Models\WorkUnit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkUnitController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $workUnits = WorkUnit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return WorkUnitResource::collection($workUnits);
    }
}
