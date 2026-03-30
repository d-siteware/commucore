<?php

declare(strict_types=1);

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $token
 * @property string $email
 * @property string $name
 * @property string|null $first_name
 * @property string|null $gender
 * @property Carbon|null $birth_date
 * @property string|null $birth_place
 * @property string $locale
 * @property string|null $address
 * @property string|null $zip
 * @property string|null $city
 * @property string $country
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $family_status
 * @property string|null $type
 * @property bool $is_deducted
 * @property string|null $deduction_reason
 * @property Carbon|null $applied_at
 * @property Carbon|null $verified_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $rejected_at
 * @property string|null $rejection_reason
 * @property Carbon|null $expires_at
 * @property Carbon|null $gdpr_consent_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $newsletter_consent_at
 * @property Carbon $photo_consent_at
 */
final class MemberApplication extends Model
{
    use Notifiable;

    protected $table = 'member_applications';

    protected $fillable = [
        'token',
        'email',
        'name',
        'first_name',
        'gender',
        'birth_date',
        'birth_place',
        'locale',
        'address',
        'zip',
        'city',
        'country',
        'phone',
        'mobile',
        'family_status',
        'type',
        'is_deducted',
        'deduction_reason',
        'applied_at',
        'verified_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'expires_at',
        'gdpr_consent_at',
        'newsletter_consent_at',
        'photo_consent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'applied_at' => 'datetime',
            'verified_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'expires_at' => 'datetime',
            'gdpr_consent_at' => 'datetime',
            'newsletter_consent_at' => 'datetime',
            'photo_consent_at' => 'datetime',
            'is_deducted' => 'boolean',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function createFromFormData(array $data): self
    {
        /** @var self $application */
        $application = self::create([
            ...$data,
            'token' => Str::random(64),
            'applied_at' => now(),
            'expires_at' => now()->addHours(48),
        ]);

        return $application;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isRejected(): bool
    {
        return $this->rejected_at !== null;
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->rejected_at === null;
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
