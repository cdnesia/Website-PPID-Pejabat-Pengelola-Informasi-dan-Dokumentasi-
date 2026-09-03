<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Collection
    {
        return Role::query()->pluck('name');
    }
}
