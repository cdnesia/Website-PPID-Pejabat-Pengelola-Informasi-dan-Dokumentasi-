<?php

namespace Database\Factories;

use App\Models\InformationRequest;
use App\Models\Objection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Objection>
 */
class ObjectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => InformationRequest::factory(),
            'user_id' => User::factory(),
            'reason' => $this->faker->paragraph(),
            'status' => 'submitted',
        ];
    }
}
