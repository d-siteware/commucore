<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Enums\ReportStatus;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountReport>
 */
final class AccountReportFactory extends Factory
{
    protected $model = AccountReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'starting_amount' => $this->faker->numberBetween(1000, 1000000),
            'end_amount' => $this->faker->numberBetween(1000, 1000000),
            'total_income' => $this->faker->numberBetween(100, 100000),
            'total_expenditure' => $this->faker->numberBetween(100, 100000),
            'period_start' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'period_end' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'account_id' => Account::factory(), // Adjust based on your setup
            'created_by' => User::factory(),
            'status' => $this->faker->randomElement(ReportStatus::toArray()),
        ];
    }
}
