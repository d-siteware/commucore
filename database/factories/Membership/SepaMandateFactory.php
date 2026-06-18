<?php

declare(strict_types=1);

namespace Database\Factories\Membership;

use App\Enums\SepaMandateStatus;
use App\Enums\SepaMandateType;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use Illuminate\Database\Eloquent\Factories\Factory;

final class SepaMandateFactory extends Factory
{
    protected $model = SepaMandate::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'mandate_reference' => 'SEPA-'.fake()->unique()->randomNumber(8),
            'iban' => 'DE89370400440532013000',
            'bic' => 'COBADEFFXXX',
            'account_holder' => fake()->name(),
            'mandate_date' => fake()->dateTimeThisYear(),
            'mandate_type' => SepaMandateType::Core,
            'status' => SepaMandateStatus::Active,
            'payment_completed_at' => null,
        ];
    }

    public function b2b(): static
    {
        return $this->state(['mandate_type' => SepaMandateType::B2b]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => SepaMandateStatus::Cancelled]);
    }

    public function paymentCompleted(): static
    {
        return $this->state(['payment_completed_at' => now()]);
    }
}
