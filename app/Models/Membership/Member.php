<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Enums\Gender;
use App\Enums\MemberFamilyStatus;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Enums\SepaMandateStatus;
use App\Enums\TransactionStatus;
use App\Models\Accounting\Transaction;
use App\Models\History;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasHistory;
use App\Models\User;
use Database\Factories\Membership\MemberFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $applied_at
 * @property Carbon|null $verified_at
 * @property Carbon|null $entered_at
 * @property Carbon|null $left_at
 * @property bool $is_deducted
 * @property string|null $deduction_reason
 * @property Carbon|null $birth_date
 * @property string $name
 * @property string|null $first_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $address
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $country
 * @property string|null $locale
 * @property Gender|null $gender
 * @property MemberType $type
 * @property string|null $birth_place
 * @property string|null $citizenship
 * @property MemberFamilyStatus|null $family_status
 * @property MemberFeeType $fee_type
 * @property Carbon|null $gdpr_consent_at
 * @property string|null $gdpr_legal_basis
 * @property Carbon|null $newsletter_consent_at
 * @property Carbon|null $newsletter_consent_revoked_at
 * @property Carbon|null $photo_consent_at
 * @property Carbon|null $photo_consent_revoked_at
 * @property Carbon|null $pseudonymized_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read User|null $user
 *
 * @method static MemberFactory factory($count = null, $state = [])
 * @method static Builder<static>|Member newModelQuery()
 * @method static Builder<static>|Member newQuery()
 * @method static Builder<static>|Member query()
 * @method static Builder<static>|Member whereAddress($value)
 * @method static Builder<static>|Member whereAppliedAt($value)
 * @method static Builder<static>|Member whereBirthDate($value)
 * @method static Builder<static>|Member whereBirthPlace($value)
 * @method static Builder<static>|Member whereCitizenship($value)
 * @method static Builder<static>|Member whereCity($value)
 * @method static Builder<static>|Member whereCountry($value)
 * @method static Builder<static>|Member whereCreatedAt($value)
 * @method static Builder<static>|Member whereDeductionReason($value)
 * @method static Builder<static>|Member whereEmail($value)
 * @method static Builder<static>|Member whereEnteredAt($value)
 * @method static Builder<static>|Member whereFamilyStatus($value)
 * @method static Builder<static>|Member whereFeeType($value)
 * @method static Builder<static>|Member whereFirstName($value)
 * @method static Builder<static>|Member whereGender($value)
 * @method static Builder<static>|Member whereId($value)
 * @method static Builder<static>|Member whereIsDeducted($value)
 * @method static Builder<static>|Member whereLeftAt($value)
 * @method static Builder<static>|Member whereLocale($value)
 * @method static Builder<static>|Member whereMobile($value)
 * @method static Builder<static>|Member whereName($value)
 * @method static Builder<static>|Member wherePhone($value)
 * @method static Builder<static>|Member whereType($value)
 * @method static Builder<static>|Member whereUpdatedAt($value)
 * @method static Builder<static>|Member whereUserId($value)
 * @method static Builder<static>|Member whereVerifiedAt($value)
 * @method static Builder<static>|Member whereGdpr_consent_at($value)
 * @method static Builder<static>|Member whereGdpr_legal_basis($value)
 * @method static Builder<static>|Member whereNewsletter_consent_at($value)
 * @method static Builder<static>|Member whereNewsletter_consent_revoked_at($value)
 * @method static Builder<static>|Member wherePhoto_consent_at($value)
 * @method static Builder<static>|Member wherePhoto_consent_revoked_at($value)
 * @method static Builder<static>|Member wherePseudonymized_at($value)
 *
 * @property-read Collection<int, History> $histories
 * @property-read int|null $histories_count
 * @property-read MemberRole|null $pivot
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, Role> $activeRoles
 * @property-read int|null $active_roles_count
 * @property-read Collection<int, MemberTransaction> $memberTransactions
 * @property-read int|null $member_transactions_count
 *
 * @mixin Eloquent
 */
final class Member extends Model
{
    use HasDocuments;

    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    use HasHistory;
    use Notifiable;

    public static int $age_discounted = 65;

    public static int $age_free = 80;

    protected $guarded = [];

    protected $casts = [
        'applied_at' => 'datetime',
        'verified_at' => 'datetime',
        'entered_at' => 'datetime',
        'left_at' => 'datetime',
        'gdpr_consent_at' => 'datetime',
        'newsletter_consent_at' => 'datetime',
        'newsletter_consent_revoked_at' => 'datetime',
        'photo_consent_at' => 'datetime',
        'photo_consent_revoked_at' => 'datetime',
        'pseudonymized_at' => 'datetime',
        'birth_date' => 'datetime',
        'is_deducted' => 'boolean',
        'type' => MemberType::class,
        'family_status' => MemberFamilyStatus::class,
        'fee_type' => MemberFeeType::class,
        'gender' => Gender::class,
        'iban' => 'encrypted',
        'bic' => 'encrypted',
        'account_holder' => 'encrypted',
    ];

    public function fullName(): string
    {
        return $this->name.', '.$this->first_name;
    }

    public static function feeForHumans(int $value): string
    {
        return number_format($value / 100, 2, ',', '.');
    }

    public static function getBoardMembers(): object
    {
        return Member::query()->whereIn('type', [MemberType::AD->value, MemberType::MD->value])
            ->get();
    }

    public static function countNewApplicants(): int
    {
        return Member::query()->whereIn('type', [MemberType::AP->value])
            ->count();
    }

    public static function Applicants(): Collection
    {
        return Member::query()->whereIn('type', [MemberType::AP->value])
            ->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasUser(): bool
    {
        return $this->user->hasAttribute('email') && $this->user->email;
    }

    public function memberTransactions(): HasMany
    {
        return $this->hasMany(MemberTransaction::class, 'member_id');
    }

    public function transactions(): HasManyThrough
    {
        //        return $this->hasMany(Transaction::class);
        return $this->hasManyThrough(Transaction::class, MemberTransaction::class, 'member_id', 'id', 'id', 'transaction_id');
    }

    public function sepaMandates(): HasMany
    {
        return $this->hasMany(SepaMandate::class);
    }

    public function activeSepaMandate(): HasMany
    {
        return $this->hasMany(SepaMandate::class)
            ->where('status', SepaMandateStatus::Active);
    }

    public function sepaCollectionAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\Sepa\SepaCollectionAttempt::class);
    }

    public function feeStatus(): array
    {
        $totalFee = $this->fee_type->fee() * 12;

        //        if ($this->fee_type === MemberFeeType::FREE->value){
        //            return [
        //                'paid' => $totalFee,
        //                'total' => $totalFee,
        //                'status' => true
        //            ];
        //        }
        $paidFee = 0;
        $currentYear = Carbon::now('Europe/Berlin')->year;
        $payments = MemberTransaction::query()
            ->where('member_id', $this->id)
            ->with(['transaction' => function ($query): void {
                $query->select('id', 'amount_gross', 'label', 'status')
                    ->whereBetween('date', [Carbon::now('Europe/Berlin')->startOfYear(), Carbon::now('Europe/Berlin')->endOfYear()])
                    ->where('booking_account_id', '=', 13)
                    ->where('status', TransactionStatus::booked->value)
                    ->orWhere('label', 'LIKE', '%'.Carbon::now('Europe/Berlin')->year.'%')
                    ->orWhere('label', 'LIKE', '%betrag%'); // Select columns from the transaction table
            }])
            ->whereBetween('updated_at', [
                Carbon::today()->startOfYear(), Carbon::now('Europe/Berlin'),
            ])
            ->get();

        foreach ($payments as $payment) {
            if ($payment->transaction) {
                $paidFee += $payment->transaction->amount_gross;
            }
        }

        return ['paid' => $paidFee / 100, 'total' => $totalFee / 100, 'status' => $paidFee >= $totalFee];
    }

    public function checkInvitationStatus(): string
    {

        $invitation = Invitation::query()->where('email', $this->email)->first();

        if ($invitation) {
            return $invitation->accepted ? 'accepted' : 'invited';
        }

        return 'none';

    }

    public function hasBirthdayToday(): bool
    {
        return $this->birth_date->format('d') === Carbon::today('Europe/Berlin')->format('d');
    }

    public function birthDayInMonth(): string
    {

        return Carbon::create(date('Y'), (int) $this->birth_date->format('m'), (int) $this->birth_date->format('d'))
            ->isoFormat('Do dddd');

    }

    public function age(): int
    {
        return (int) $this->birth_date->diffInYears();
    }

    public static function leaderBoardString(string $locale = 'de'): string
    {
        // In Tests wird kein Cache verwendet, daher direkt berechnen
        if (app()->environment('testing')) {
            return self::buildLeaderBoardString($locale);
        }

        return cache()->remember("leaderboard_{$locale}", 3600, function () use ($locale): string {
            return self::buildLeaderBoardString($locale);
        });
    }

    private static function buildLeaderBoardString(string $locale): string
    {
        $string = '';
        $roles = Role::with('members')->get();

        foreach ($roles as $role) {
            if ($role->members->count() > 0) {
                $string .= $role->name[$locale].': ';
                $string .= $role->members->first()->fullName();
                $string .= ' ';
            }
        }

        return $string;
    }

    public static function organizationRepresentativeString(string $locale = 'de'): string
    {
        // In Tests wird kein Cache verwendet, daher direkt berechnen
        if (app()->environment('testing')) {
            return self::buildOrganizationRepresentativeString($locale);
        }

        return cache()->remember("leaderboard_{$locale}", 3600, function () use ($locale): string {
            return self::buildOrganizationRepresentativeString($locale);
        });
    }

    private static function buildOrganizationRepresentativeString(string $locale = 'de'): string
    {
        $string = '';
        $roles = Role::with('members')->where('can_represent_organization', true)->get();

        foreach ($roles as $role) {
            if ($role->members->count() > 0) {
                $string .= $role->name[$locale].': ';
                $string .= $role->members->first()->fullName();
                $string .= ' ';
            }
        }

        return $string;
    }

    public static function leaderBoardHtml(string $locale = 'hu'): string
    {

        $string = '<div style="font-size: 12px; line-height: 1.2; margin-bottom: 10px;">';

        foreach (Role::all() as $roleItem) {

            if ($roleItem->members->count() > 0) {
                $string .= '<span style="font-weight: bold; margin-right: 1px;">'.$roleItem->name[$locale].': </span> ';
                $string .= '<span style=" margin-right: 3px;">'.$roleItem->members->first()->fullName().'</span>';
            }
            //
        }

        return $string.'</div>';
    }

    /**
     * Beiträge für ein bestimmtes Jahr
     */
    public function membershipFeesForYear(int $year): Builder
    {
        return MemberTransaction::query()
            ->where('member_id', $this->id)
            ->membershipFees()
            ->forYear($year)
            ->with('transaction');
    }

    /**
     * Summe der bezahlten Beiträge für ein Jahr
     */
    public function totalPaidFeesForYear(int $year): int
    {
        return MemberTransaction::query()
            ->where('member_id', $this->id)
            ->membershipFees()
            ->forYear($year)
            ->paid()
            ->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
            ->sum('transactions.amount_net');
    }

    /**
     * Status-Check
     */
    public function hasPaidFeeForYear(int $year): bool
    {
        return MemberTransaction::query()
            ->where('member_id', $this->id)
            ->membershipFees()
            ->forYear($year)
            ->paid()
            ->exists();
    }

    /**
     * Für Übersicht: Alle Jahre mit Beitragszahlungen
     */
    public function getFeeYearsWithStatus(): Collection|\Illuminate\Support\Collection
    {
        return MemberTransaction::query()
            ->where('member_id', $this->id)
            ->membershipFees()
            ->with('transaction')
            ->get()
            ->groupBy('fee_year')
            ->map(function ($transactions, $year): array {
                $paid = $transactions->filter(fn ($t): bool => $t->transaction->status === TransactionStatus::booked);

                return [
                    'year' => $year,
                    'total_paid' => $paid->sum(fn ($t) => $t->transaction->amount_net),
                    'total_pending' => $transactions->filter(fn ($t): bool => $t->transaction->status === TransactionStatus::submitted)
                        ->sum(fn ($t) => $t->transaction->amount_net),
                    'transaction_count' => $transactions->count(),
                    'paid_count' => $paid->count(),
                ];
            });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'member_role')
            ->withPivot('designated_at', 'resigned_at', 'about_me', 'profile_image')
            ->withTimestamps()
            ->using(MemberRole::class);
    }

    public function activeRoles(): BelongsToMany
    {
        return $this->roles()->wherePivot('resigned_at', null);
    }

    /**
     * Prüfe ob Member Buchhaltungsrechte hat
     */
    public function hasAccountingRights(): bool
    {
        return $this->activeRoles()
            ->where('can_manage_accounting', true)
            ->exists();
    }

    /**
     * Prüfe ob Member im Vorstand ist (nutzt MemberType)
     */
    public function isBoardMember(): bool
    {
        return $this->type === MemberType::MD;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class, 'member_id');
    }

    public function isPseudonymized(): bool
    {
        return $this->pseudonymized_at !== null;
    }

    public static function createFromApplication(
        MemberApplication $application,
        \Carbon\Carbon $gdprConsentAt,
        ?\Carbon\Carbon $newsletterConsentAt,
        ?\Carbon\Carbon $photoConsentAt,
    ): self {
        /** @var self $member */
        $member = self::create([
            'name' => $application->name,
            'first_name' => $application->first_name,
            'gender' => $application->gender,
            'birth_date' => $application->birth_date,
            'birth_place' => $application->birth_place,
            'locale' => $application->locale,
            'address' => $application->address,
            'zip' => $application->zip,
            'city' => $application->city,
            'country' => $application->country,
            'phone' => $application->phone,
            'mobile' => $application->mobile,
            'email' => $application->email,
            'family_status' => $application->family_status,
            'type' => $application->type ?? MemberType::AP->value,
            'is_deducted' => $application->is_deducted,
            'deduction_reason' => $application->deduction_reason,
            'applied_at' => $application->applied_at,
            'gdpr_consent_at' => $gdprConsentAt,
            'newsletter_consent_at' => $newsletterConsentAt,
            'photo_consent_at' => $photoConsentAt,
        ]);

        return $member;
    }

    public static function getAccountantUsers(): Collection
    {
        return Member::query()->whereNotNull('user_id')->whereHas('roles', function ($query) {
            return $query->where('can_manage_accounting', true);
        })->get();
    }

    public static function getAccountingUsers(): Collection
    {
        return Member::query()->whereNotNull('user_id')->whereHas('roles', function ($query) {
            return $query->where('can_manage_accounting', true)->orWhere('can_audit_accounting', true);
        })->get();
    }
}
