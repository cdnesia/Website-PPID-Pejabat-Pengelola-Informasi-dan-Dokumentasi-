<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ProductionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the initial super admin account from environment variables.
     * Safe to run multiple times: it never overwrites an existing user's
     * password, it only assigns the role if missing.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in your environment before running ProductionUserSeeder.');
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Super Admin PPID'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command->info("Super admin ready: {$email}");
    }
}
