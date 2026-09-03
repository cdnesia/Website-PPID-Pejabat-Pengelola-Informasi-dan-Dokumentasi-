<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNewsTest extends TestCase
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

    public function test_creating_a_published_news_sets_published_at(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/news', [
            'title' => 'Berita Uji Coba',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Isi berita</p>',
            'category' => 'Pengumuman',
            'is_published' => true,
        ]);

        $response->assertCreated();

        $news = News::where('title', 'Berita Uji Coba')->firstOrFail();

        $this->assertTrue($news->is_published);
        $this->assertNotNull($news->published_at);
    }

    public function test_unpublishing_a_news_clears_published_at(): void
    {
        $admin = $this->actingAsAdmin();

        $news = News::factory()->create([
            'is_published' => true,
            'published_at' => now(),
            'author_id' => $admin->id,
        ]);

        $response = $this->putJson("/api/admin/news/{$news->slug}", [
            'title' => $news->title,
            'content' => $news->content,
            'is_published' => false,
        ]);

        $response->assertOk();

        $this->assertNull($news->refresh()->published_at);
        $this->assertFalse($news->is_published);
    }

    public function test_admin_show_endpoint_returns_full_content(): void
    {
        $admin = $this->actingAsAdmin();

        $news = News::factory()->create([
            'content' => '<p>Konten lengkap berita.</p>',
            'author_id' => $admin->id,
        ]);

        $response = $this->getJson("/api/admin/news/{$news->slug}");

        $response->assertOk()->assertJsonPath('data.content', '<p>Konten lengkap berita.</p>');
    }

    public function test_public_show_endpoint_returns_full_content(): void
    {
        $admin = $this->actingAsAdmin();

        $news = News::factory()->create([
            'content' => '<p>Konten publik.</p>',
            'is_published' => true,
            'published_at' => now(),
            'author_id' => $admin->id,
        ]);

        $response = $this->getJson("/api/news/{$news->slug}");

        $response->assertOk()->assertJsonPath('data.content', '<p>Konten publik.</p>');
    }

    public function test_public_index_endpoint_omits_full_content(): void
    {
        $admin = $this->actingAsAdmin();

        News::factory()->create([
            'content' => '<p>Konten yang tidak perlu tampil di daftar.</p>',
            'is_published' => true,
            'published_at' => now(),
            'author_id' => $admin->id,
        ]);

        $response = $this->getJson('/api/news');

        $response->assertOk()->assertJsonMissingPath('data.0.content');
    }
}
