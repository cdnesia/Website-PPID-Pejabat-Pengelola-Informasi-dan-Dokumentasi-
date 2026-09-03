<?php

namespace Database\Factories;

use App\Models\InformationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InformationRequest>
 */
class InformationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'applicant_name' => $this->faker->name(),
            'applicant_nik' => $this->faker->numerify('################'),
            'applicant_occupation' => $this->faker->jobTitle(),
            'applicant_phone' => $this->faker->phoneNumber(),
            'applicant_email' => $this->faker->safeEmail(),
            'applicant_address' => $this->faker->address(),
            'purpose' => $this->faker->sentence(10),
            'information_detail' => $this->faker->paragraph(),
            'format_requested' => $this->faker->randomElement(['digital', 'cetak']),
            'delivery_method' => $this->faker->randomElement(['email', 'datang_langsung', 'pos', 'whatsapp']),
            'response_delivery_method' => $this->faker->randomElement(['email', 'pos', 'diambil_langsung']),
            'status' => 'submitted',
            'due_date' => now()->addWeekdays(10),
        ];
    }
}
