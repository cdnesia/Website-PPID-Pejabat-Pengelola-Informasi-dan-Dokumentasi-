<?php

namespace Database\Factories;

use App\Models\InformationRequest;
use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestLog>
 */
class RequestLogFactory extends Factory
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
            'action' => 'status_changed',
            'description' => $this->faker->sentence(),
            'old_status' => 'submitted',
            'new_status' => 'in_review',
        ];
    }
}
