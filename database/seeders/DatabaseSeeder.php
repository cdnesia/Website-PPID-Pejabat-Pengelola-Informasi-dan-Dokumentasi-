<?php

namespace Database\Seeders;

use App\Models\InformationCategory;
use App\Models\News;
use App\Models\PublicInformation;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(PageSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin PPID',
            'email' => 'superadmin@ppid.test',
        ]);
        $superAdmin->assignRole('super_admin');

        $this->command->info('Super admin login: superadmin@ppid.test / password');

        $workUnits = WorkUnit::factory(4)->create();

        $categories = collect(['berkala', 'serta_merta', 'setiap_saat', 'dikecualikan'])
            ->map(fn (string $type) => InformationCategory::factory()->create(['type' => $type]));

        $categories->each(function (InformationCategory $category) use ($workUnits, $superAdmin) {
            PublicInformation::factory(3)->create([
                'category_id' => $category->id,
                'work_unit_id' => $workUnits->random()->id,
                'created_by' => $superAdmin->id,
            ]);
        });

        News::factory(6)->create(['author_id' => $superAdmin->id]);
    }
}
