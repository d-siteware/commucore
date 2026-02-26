<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Models\Accounting\FiscalYear;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FiscalYear>
 */
final class FiscalYearFactory extends Factory
{
    protected $model = FiscalYear::class;

    private static int $yearCounter = 2020;

    public function definition(): array
    {
        $year = self::$yearCounter++;

        return [
            'year' => $year,
            'opened_at' => Carbon::create($year, 1, 1),
            'closed_at' => null,
            'opened_by' => User::factory(),
            'closed_by' => null,
        ];
    }

    /**
     * Geschlossenes Geschäftsjahr mit optionalem User
     */
    public function closed(?int $closedByUserId = null): static
    {
        return $this->state(function (array $attributes) use ($closedByUserId) {
            $openedAt = $this->ensureCarbon($attributes['opened_at']);

            return [
                'closed_at' => $openedAt->copy()->endOfYear(),
                'closed_by' => $closedByUserId ?? User::factory(),
            ];
        });
    }

    /**
     * Geschlossenes Jahr mit spezifischem Datum
     */
    public function closedAt(Carbon|string $closedAt): static
    {
        return $this->state(fn (array $attributes) => [
            'closed_at' => $this->ensureCarbon($closedAt),
            'closed_by' => User::factory(),
        ]);
    }

    /**
     * Spezifisches Jahr
     */
    public function forYear(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'opened_at' => Carbon::create($year, 1, 1),
        ]);
    }

    /**
     * Offenes Jahr (explizit)
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'closed_at' => null,
            'closed_by' => null,
        ]);
    }

    /**
     * Mit spezifischen Usern
     */
    public function openedBy(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'opened_by' => $userId,
        ]);
    }

    public function closedBy(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'closed_by' => $userId,
        ]);
    }

    /**
     * Helper: Stelle sicher dass ein Wert ein Carbon ist
     */
    private function ensureCarbon(mixed $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value)) {
            return Carbon::parse($value);
        }

        if ($value instanceof \DateTime) {
            return Carbon::instance($value);
        }

        throw new \InvalidArgumentException('Cannot convert value to Carbon instance');
    }

    /**
     * Reset counter
     */
    public static function resetYearCounter(): void
    {
        self::$yearCounter = 2020;
    }
}
