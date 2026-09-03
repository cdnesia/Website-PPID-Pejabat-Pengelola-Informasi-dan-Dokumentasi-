<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): UserResource
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'nik' => $request->validated('nik'),
            'phone' => $request->validated('phone'),
            'address' => $request->validated('address'),
        ]);

        $user->assignRole('pemohon');

        event(new Registered($user));

        Auth::login($user);

        return new UserResource($user);
    }
}
