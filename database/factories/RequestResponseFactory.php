<?php

namespace Database\Factories;

use App\Models\InformationRequest;
use App\Models\RequestResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestResponse>
 */
class RequestResponseFactory extends Factory
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
            'admin_id' => User::factory(),
            'response_text' => $this->faker->paragraph(),
            'responded_at' => now(),
        ];
    }
}
