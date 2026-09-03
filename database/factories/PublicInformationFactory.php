<?php

namespace Database\Factories;

use App\Models\InformationCategory;
use App\Models\PublicInformation;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PublicInformation>
 */
class PublicInformationFactory extends Factory
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
            'category_id' => InformationCategory::factory(),
            'work_unit_id' => WorkUnit::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->sentence(15),
            'content' => $this->faker->paragraphs(3, true),
            'status' => 'published',
            'published_at' => now(),
            'created_by' => User::factory(),
        ];
    }
}
