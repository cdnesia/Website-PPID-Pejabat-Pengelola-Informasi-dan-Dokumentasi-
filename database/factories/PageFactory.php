<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->sentence(8),
            'content' => ['body' => $this->faker->paragraph()],
        ];
    }
}
