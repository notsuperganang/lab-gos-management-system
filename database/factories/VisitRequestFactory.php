<?php

namespace Database\Factories;

use App\Models\VisitRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitRequestFactory extends Factory
{
    protected $model = VisitRequest::class;

    public function definition(): array
    {
        return [
            'request_id' => 'VR' . now()->format('Ymd') . str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->safeEmail(),
            'visitor_phone' => '+6285' . fake()->numerify('########'),
            'institution' => fake()->company(),
            'visit_purpose' => fake()->randomElement(['study-visit', 'research', 'learning', 'internship', 'others']),
            'visit_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'group_size' => fake()->numberBetween(1, 20),
            'purpose_description' => fake()->sentence(),
            'special_requirements' => fake()->optional()->sentence(),
            'equipment_needed' => [],
            'submitted_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => 1,
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => 1,
            'approval_notes' => 'Request rejected for testing purposes',
        ]);
    }
}