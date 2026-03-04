<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Enums\ExportType;
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
    public static function toStream(Collection $members, ExportType $type): mixed
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
    private static function headers(ExportType $type): array
    {
        $base = [
            'ID', 'Name', 'Vorname', 'E-Mail', 'Telefon', 'Mobil',
            'Adresse', 'PLZ', 'Ort', 'Land', 'Sprache', 'Geschlecht',
        ];

        if ($type === ExportType::STAMMDATEN) {
            return $base;
        }

        // MEMBERS_ALL
        return array_merge($base, [
            'Typ', 'Beitragstyp', 'Familienstand', 'Geburtsdatum',
            'Geburtsort', 'Staatsangehörigkeit', 'Eingetreten', 'Ausgetreten',
            'Beantragt', 'Verifiziert', 'Beitragsbefreiung', 'Befreiungsgrund',
            'Aktive Rollen', 'Pseudonymisiert',
        ]);
    }

    /**
     * @return array<int, string|null>
     */
    private static function row(Member $member, ExportType $type): array
    {
        $base = [
            (string) $member->id,
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
            $member->gender?->value,
        ];

        if ($type === ExportType::STAMMDATEN) {
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
            $member->type->value,
            $member->fee_type->value,
            $member->family_status,
            $member->birth_date?->toDateString(),
            $member->birth_place,
            $member->citizenship,
            $member->entered_at?->toDateString(),
            $member->left_at?->toDateString(),
            $member->applied_at->toDateString(),
            $member->verified_at?->toDateString(),
            $member->is_deducted ? 'ja' : 'nein',
            $member->deduction_reason,
            $roles,
            $member->pseudonymized_at?->toDateString(),
        ]);
    }
}
