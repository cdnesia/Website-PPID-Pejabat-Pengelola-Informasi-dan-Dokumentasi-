<?php

namespace Database\Factories;

use App\Models\WorkUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkUnit>
 */
class WorkUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('OPD-???')),
            'name' => 'Dinas '.$this->faker->unique()->words(2, true),
            'head_name' => $this->faker->name(),
            'head_title' => 'Kepala Dinas',
            'description' => $this->faker->sentence(12),
            'is_active' => true,
        ];
    }
}
