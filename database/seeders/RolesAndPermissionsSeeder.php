<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage-requests',
            'manage-informations',
            'manage-news',
            'manage-pages',
            'manage-users',
            'manage-settings',
            'view-reports',
            'approve-objections',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'admin_ppid_utama'])
            ->syncPermissions(['manage-requests', 'view-reports']);

        Role::firstOrCreate(['name' => 'admin_ppid_pembantu'])
            ->syncPermissions(['manage-informations', 'manage-news', 'manage-pages']);

        Role::firstOrCreate(['name' => 'pimpinan'])
            ->syncPermissions(['view-reports', 'approve-objections']);

        Role::firstOrCreate(['name' => 'pemohon']);
    }
}
