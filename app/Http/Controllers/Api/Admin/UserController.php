<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->with('roles')
            ->when($request->string('role')->isNotEmpty(), fn ($query) => $query->role($request->string('role')->toString()))
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where(
                fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('email', 'like', '%'.$request->string('q').'%'),
            ))
            ->latest()
            ->paginate(20);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'phone' => $request->validated('phone'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->validated('role'));

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update(array_filter([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
        ], fn ($value) => $value !== null));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->validated('password'))]);
        }

        if ($request->filled('role')) {
            $user->syncRoles([$request->validated('role')]);
        }

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user): Response
    {
        abort_if($user->id === $request->user()->id, 422, 'Tidak dapat menghapus akun sendiri.');

        $user->delete();

        return response()->noContent();
    }

    public function resetPassword(User $user): JsonResponse
    {
        Password::sendResetLink(['email' => $user->email]);

        return response()->json(['message' => "Tautan reset kata sandi telah dikirim ke {$user->email}."]);
    }
}
