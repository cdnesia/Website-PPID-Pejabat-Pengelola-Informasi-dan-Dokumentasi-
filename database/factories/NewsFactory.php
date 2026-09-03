<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->sentence(20),
            'content' => $this->faker->paragraphs(4, true),
            'category' => $this->faker->randomElement(['Pengumuman', 'Kegiatan', 'Rilis Media']),
            'is_published' => true,
            'published_at' => now(),
            'author_id' => User::factory(),
            'view_count' => $this->faker->numberBetween(0, 500),
        ];
    }
}
