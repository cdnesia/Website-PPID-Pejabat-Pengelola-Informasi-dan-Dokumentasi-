<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        return $admin;
    }

    public function test_admin_can_upload_a_logo(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Setting::current();

        $response = $this->post('/api/admin/settings', [
            '_method' => 'PUT',
            'org_name' => 'PPID UM Jambi',
            'response_deadline_days' => 10,
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertOk();
        $this->assertNotNull($response->json('data.logo_url'));

        $setting = Setting::current();
        $this->assertTrue($setting->getFirstMedia('logo') !== null);
    }

    public function test_updating_settings_without_a_logo_keeps_the_existing_one(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        Setting::current()->addMedia(UploadedFile::fake()->image('logo.png'))->toMediaCollection('logo');

        $response = $this->putJson('/api/admin/settings', [
            'org_name' => 'PPID UM Jambi Updated',
            'response_deadline_days' => 10,
        ]);

        $response->assertOk();
        $this->assertNotNull($response->json('data.logo_url'));
    }
}
