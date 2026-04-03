<?php

namespace Database\Factories;

use App\Enums\MemberChangeField;
use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MemberChangeRequest>
 */
class MemberChangeRequestFactory extends Factory
{
    public function definition(): array
    {
        $cases = MemberChangeField::cases();
        $field = $cases[array_rand($cases)];

        return [
            'member_id' => Member::factory(),
            'field' => $field->value,
            'old_value' => 'old',
            'requested_value' => 'new',
            'reason' => $this->faker->sentence(),
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'completed_at' => null,
            'rejected_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['completed_at' => null, 'rejected_at' => null]);
    }

    public function completed(): static
    {
        return $this->state([
            'completed_at' => now(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'rejected_at' => now(),
            'reviewed_at' => now(),
            'rejection_reason' => 'Nicht nachvollziehbar.',
        ]);
    }
}
