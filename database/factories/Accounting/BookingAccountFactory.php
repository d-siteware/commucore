<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use App\Models\Accounting\BookingAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingAccount>
 */
final class BookingAccountFactory extends Factory
{
    protected $model = BookingAccount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('####'),
            'label' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(AccountCategory::cases())->value,
            'subtype' => null,
            'area' => $this->faker->randomElement(BookingAccountArea::cases())->value,
        ];
    }

    // ==================== States ====================

    public function income(): static
    {
        return $this->state([
            'category' => AccountCategory::Income->value,
            'subtype' => null,
        ]);
    }

    public function expense(): static
    {
        return $this->state([
            'category' => AccountCategory::Expense->value,
            'subtype' => null,
        ]);
    }

    public function bank(): static
    {
        return $this->state([
            'category' => AccountCategory::Asset->value,
            'subtype' => AccountSubtype::Bank->value,
        ]);
    }

    public function cash(): static
    {
        return $this->state([
            'category' => AccountCategory::Asset->value,
            'subtype' => AccountSubtype::Cash->value,
        ]);
    }

    public function ideal(): static
    {
        return $this->state([
            'area' => BookingAccountArea::IDEAL->value,
        ]);
    }
}
