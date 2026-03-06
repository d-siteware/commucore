<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use Database\Factories\BookingAccountFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $type
 * @property string $number
 * @property string $label
 * @property string $area
 *
 * @method static Builder<static>|BookingAccount newModelQuery()
 * @method static Builder<static>|BookingAccount newQuery()
 * @method static Builder<static>|BookingAccount query()
 * @method static Builder<static>|BookingAccount whereCreatedAt($value)
 * @method static Builder<static>|BookingAccount whereId($value)
 * @method static Builder<static>|BookingAccount whereLabel($value)
 * @method static Builder<static>|BookingAccount whereNumber($value)
 * @method static Builder<static>|BookingAccount whereArea($value)
 * @method static Builder<static>|BookingAccount whereType($value)
 * @method static Builder<static>|BookingAccount whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class BookingAccount extends Model
{
    /**
     *   Standardkontenrahmen 49
     *   https://www.standardkontenrahmen.de/skr49
     */

    /** @use HasFactory<BookingAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'label',
        'number',
        'type',
        'area',
    ];
}
