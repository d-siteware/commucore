<?php

namespace Database\Factories;

use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MemberCancellationRequest>
 */
class MemberCancellationRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'reason' => $this->faker->paragraph(),
            'requested_leave_date' => null,
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'confirmed_at' => null,
            'rejected_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'confirmed_at' => null,
            'rejected_at' => null,
        ]);
    }

    public function withLeaveDate(): static
    {
        return $this->state([
            'requested_leave_date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state([
            'confirmed_at' => now(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'rejected_at' => now(),
            'reviewed_at' => now(),
            'rejection_reason' => 'Antrag nicht nachvollziehbar.',
        ]);
    }
}
