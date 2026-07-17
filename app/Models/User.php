<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\MemberType;
use App\Models\Accounting\CancelTransaction;
use App\Models\Membership\Member;
use App\Models\Traits\HasHistory;
use App\Notifications\CustomResetPassword;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $first_name
 * @property string|null $gender
 * @property string|null $username
 * @property string|null $phone
 * @property string|null $mobile
 * @property bool|null $is_admin
 * @property string|null $locale
 * @property Collection $unreadNotifications
 * @property-read Collection<int, CancelTransaction> $canceled_transactions
 * @property-read int|null $canceled_transactions_count
 * @property-read int|null $mail_history_entry_count
 * @property-read Member|null $member
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read string $profile_photo_url
 * @property Carbon|null $onboarding_checklist_dismissed_at
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereCurrentTeamId($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereGender($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsAdmin($value)
 * @method static Builder<static>|User whereLocale($value)
 * @method static Builder<static>|User whereMobile($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhone($value)
 * @method static Builder<static>|User whereProfilePhotoPath($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUsername($value)
 * @method static Builder<static>|User whereHasMember($value)
 *
 * @property-read Collection<int, History> $histories
 * @property-read int|null $histories_count
 *
 * @mixin Eloquent
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasHistory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'first_name',
        'gender',
        'username',
        'phone',
        'mobile',
        'email',
        'password',
        'locale',
        'email_verified_at',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'is_admin',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'onboarding_checklist_dismissed_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function canceled_transactions(): HasMany
    {
        return $this->hasMany(CancelTransaction::class);
    }

    /**
     * Lokaler Initialen-Avatar als SVG-Data-URI (Core Blue aus BRAND.md).
     * Ersetzt den frueheren ui-avatars.com-Request – keine Mitgliedernamen
     * mehr an Drittdienste (DSGVO).
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $initials = mb_strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->name, 0, 1));
        if ($initials === '') {
            $initials = '?';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
            .'<rect width="100" height="100" fill="#2563EB" fill-opacity="0.1"/>'
            .'<text x="50" y="53" text-anchor="middle" dominant-baseline="central" font-family="sans-serif" font-size="42" font-weight="600" fill="#2563EB">'
            .$initials
            .'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function isAccountant(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if (! $this->member) {
            return false;
        }

        // Prüfe ob der Member eine Rolle mit Accounting-Rechten hat
        return $this->member->roles()
            ->wherePivot('resigned_at', null)
            ->where('can_manage_accounting', true)
            ->exists();
    }

    public function isBoardMember(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if (! $this->member) {
            return false;
        }

        return $this->member->type === MemberType::MD;
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }

    /**
     * Hole alle aktiven Rollen des Users
     */
    public function getActiveRoles()
    {
        if (! $this->member) {
            return collect();
        }

        return $this->member->roles()
            ->wherePivot('resigned_at', null)
            ->get();
    }

    /**
     * Prüfe ob User eine bestimmte Permission hat
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if (! $this->member) {
            return false;
        }

        return match ($permission) {
            'manage_accounting' => $this->isAccountant(),
            'board_member' => $this->isBoardMember(),
            default => false,
        };
    }
}
