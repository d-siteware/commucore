<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Models\Traits\HasHistory;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property int $user_id
 * @property Carbon $counted_at
 * @property string $label
 * @property string|null $notes
 * @property int|null $euro_two_hundred
 * @property int|null $euro_one_hundred
 * @property int|null $euro_fifty
 * @property int|null $euro_twenty
 * @property int|null $euro_ten
 * @property int|null $euro_five
 * @property int|null $euro_two
 * @property int|null $euro_one
 * @property int|null $cent_fifty
 * @property int|null $cent_twenty
 * @property int|null $cent_ten
 * @property int|null $cent_five
 * @property int|null $cent_two
 * @property int|null $cent_one
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $account
 * @property-read User $user
 *
 * @method static Builder<static>|CashCount newModelQuery()
 * @method static Builder<static>|CashCount newQuery()
 * @method static Builder<static>|CashCount query()
 * @method static Builder<static>|CashCount whereAccountId($value)
 * @method static Builder<static>|CashCount whereCentFifty($value)
 * @method static Builder<static>|CashCount whereCentFive($value)
 * @method static Builder<static>|CashCount whereCentOne($value)
 * @method static Builder<static>|CashCount whereCentTen($value)
 * @method static Builder<static>|CashCount whereCentTwenty($value)
 * @method static Builder<static>|CashCount whereCentTwo($value)
 * @method static Builder<static>|CashCount whereCountedAt($value)
 * @method static Builder<static>|CashCount whereCreatedAt($value)
 * @method static Builder<static>|CashCount whereEuroFifty($value)
 * @method static Builder<static>|CashCount whereEuroFive($value)
 * @method static Builder<static>|CashCount whereEuroOne($value)
 * @method static Builder<static>|CashCount whereEuroOneHundred($value)
 * @method static Builder<static>|CashCount whereEuroTen($value)
 * @method static Builder<static>|CashCount whereEuroTwenty($value)
 * @method static Builder<static>|CashCount whereEuroTwo($value)
 * @method static Builder<static>|CashCount whereEuroTwoHundred($value)
 * @method static Builder<static>|CashCount whereId($value)
 * @method static Builder<static>|CashCount whereLabel($value)
 * @method static Builder<static>|CashCount whereNotes($value)
 * @method static Builder<static>|CashCount whereUpdatedAt($value)
 * @method static Builder<static>|CashCount whereUserId($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @mixin Eloquent
 */
final class CashCount extends Model
{
    use HasHistory;

    protected $guarded = [];

    protected $casts = [
        'counted_at' => 'date',
        'euro_two_hundred' => 'integer',
        'euro_one_hundred' => 'integer',
        'euro_fifty' => 'integer',
        'euro_twenty' => 'integer',
        'euro_ten' => 'integer',
        'euro_five' => 'integer',
        'euro_two' => 'integer',
        'euro_one' => 'integer',
        'cent_fifty' => 'integer',
        'cent_twenty' => 'integer',
        'cent_ten' => 'integer',
        'cent_five' => 'integer',
        'cent_two' => 'integer',
        'cent_one' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return int //  Return Value in Euro-Cent!
     */
    public static function sumCountCents(int $cash_count_id): int
    {
        $cc = CashCount::query()
            ->findOrFail($cash_count_id);

        return $cc->euro_two_hundred * 20000 + $cc->euro_one_hundred * 10000 + $cc->euro_fifty * 5000 + $cc->euro_twenty * 2000 + $cc->euro_ten * 1000 + $cc->euro_five * 500 + $cc->euro_two * 200 + $cc->euro_one * 100 + $cc->cent_fifty * 50 + $cc->cent_twenty * 20 + $cc->cent_ten * 10 + $cc->cent_five * 5 + $cc->cent_two * 2 + $cc->cent_one * 1;
    }

    public function sumString(): string
    {
        return \App\Helpers\MoneyHelper::formatCents(CashCount::sumCountCents($this->id), withSymbol: false);
    }
}
