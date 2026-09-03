<?php

namespace Database\Factories;

use App\Models\InformationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InformationCategory>
 */
class InformationCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => $this->faker->randomElement(['berkala', 'serta_merta', 'setiap_saat', 'dikecualikan']),
            'description' => $this->faker->sentence(10),
            'icon' => 'document',
            'is_active' => true,
        ];
    }
}
