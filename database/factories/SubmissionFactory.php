<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => SubmissionType::Contact,
            'status' => SubmissionStatus::New,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(),
        ];
    }
}
