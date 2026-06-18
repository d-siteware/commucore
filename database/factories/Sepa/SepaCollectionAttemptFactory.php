<?php

declare(strict_types=1);

namespace Database\Factories\Sepa;

use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaSequenceType;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Models\Sepa\SepaCollectionAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

final class SepaCollectionAttemptFactory extends Factory
{
    protected $model = SepaCollectionAttempt::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'sepa_mandate_id' => SepaMandate::factory(),
            'amount' => 6000,
            'fee_year' => now()->year,
            'remittance_information' => 'Mitgliedsbeitrag '.now()->year,
            'end_to_end_id' => 'E2E-'.fake()->unique()->randomNumber(6),
            'due_date' => now()->addDays(5),
            'sequence_type' => SepaSequenceType::Frst,
            'status' => SepaCollectionAttemptStatus::Submitted,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SepaCollectionAttemptStatus::Confirmed,
            'resolved_at' => now(),
            'transaction_id' => Transaction::factory(),
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SepaCollectionAttemptStatus::Returned,
            'resolved_at' => now(),
            'return_reason' => 'Nicht gedeckt',
        ]);
    }

    public function recurring(): static
    {
        return $this->state([
            'sequence_type' => SepaSequenceType::Rcur,
        ]);
    }

    public function withBatch(string $batchReference): static
    {
        return $this->state([
            'batch_reference' => $batchReference,
        ]);
    }
}
