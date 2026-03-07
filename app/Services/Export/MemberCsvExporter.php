<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Enums\MemberExportType;
use App\Models\Membership\Member;
use Illuminate\Support\Collection;

/**
 * Generates CSV content for member exports.
 *
 * Two modes:
 *  - STAMMDATEN:  personal contact data only
 *  - MEMBERS_ALL: all member fields + active roles
 */
final class MemberCsvExporter
{
    /**
     * @param  Collection<int, Member>  $members
     * @return resource
     */
    public static function toStream(Collection $members, MemberExportType $type): mixed
    {
        $stream = fopen('php://temp', 'r+b');

        if ($stream === false) {
            throw new \RuntimeException('Could not open temp stream for CSV export.');
        }

        // UTF-8 BOM for Excel compatibility
        fwrite($stream, "\xEF\xBB\xBF");

        fputcsv($stream, self::headers($type), ';');

        foreach ($members as $member) {
            fputcsv($stream, self::row($member, $type), ';');
        }

        rewind($stream);

        return $stream;
    }

    /**
     * @return string[]
     */
    private static function headers(MemberExportType $type): array
    {
        $base = array_values(\App\Services\Import\MemberFieldMapper::MEMBER_FIELDS);

        if ($type === MemberExportType::STAMMDATEN) {
            // Nur Stammdaten-Felder
            return array_values(array_filter(
                \App\Services\Import\MemberFieldMapper::MEMBER_FIELDS,
                static fn (string $field): bool => in_array($field, [
                    'name', 'first_name', 'email', 'phone', 'mobile',
                    'address', 'zip', 'city', 'country', 'locale', 'gender',
                ], strict: true),
                ARRAY_FILTER_USE_KEY,
            ));
        }

        return $base;
    }

    /**
     * @return array<int, string|null>
     */
    private static function row(Member $member, MemberExportType $type): array
    {
        $base = [
            $member->name,
            $member->first_name,
            $member->email,
            $member->phone,
            $member->mobile,
            $member->address,
            $member->zip,
            $member->city,
            $member->country,
            $member->locale,
        ];

        if ($type === MemberExportType::STAMMDATEN) {
            return $base;
        }

        // MEMBERS_ALL – aktive Rollen als kommaseparierter String
        $roles = $member->activeRoles
            ->map(
                static fn (\App\Models\Membership\Role $role): string => $role->name['de']
            )
            ->filter()
            ->implode(', ');

        return array_merge($base, [
            $member->gender?->value,
            $member->birth_date?->toDateString(),
            $member->birth_place,
            $member->citizenship,
            $member->family_status,
            $member->type->value,
            $member->fee_type->value,
            $member->entered_at?->toDateString(),
            $member->left_at?->toDateString(),
            $member->applied_at->toDateString(),
            $member->verified_at?->toDateString(),
            $member->is_deducted ? 'ja' : 'nein',
            $member->deduction_reason,
            $roles,
            $member->photo_consent_at?->toDateString(),
            $member->photo_consent_revoked_at?->toDateString(),
            $member->newsletter_consent_at?->toDateString(),
            $member->newsletter_consent_revoked_at?->toDateString(),
            $member->gdpr_consent_at?->toDateString(),
            $member->pseudonymized_at?->toDateString(),
        ]);
    }
}
