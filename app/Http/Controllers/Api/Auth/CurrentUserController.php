<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function __invoke(Request $request): UserResource|JsonResponse
    {
        if (! $request->user()) {
            return response()->json(['data' => null]);
        }

        return new UserResource($request->user());
    }
}
