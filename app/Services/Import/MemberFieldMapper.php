<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Enums\Gender;
use App\Enums\MemberFamilyStatus;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;

/**
 * Maps CSV headers to CommuCore member fields.
 *
 * Detects which CSV headers match known CommuCore field names,
 * which headers are unknown (need manual mapping),
 * and which Enum values in the data are unrecognised (need manual mapping).
 */
final class MemberFieldMapper
{
    /**
     * Canonical CommuCore field names with human-readable labels (DE).
     *
     * @var array<string, string>
     */
    public const MEMBER_FIELDS = [
        // Stammdaten
        'name' => 'Name',
        'first_name' => 'Vorname',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'mobile' => 'Mobil',
        'address' => 'Adresse',
        'zip' => 'PLZ',
        'city' => 'Ort',
        'country' => 'Land',
        'locale' => 'Sprache',

        // Personendaten
        'gender' => 'Geschlecht',
        'birth_date' => 'Geburtsdatum',
        'birth_place' => 'Geburtsort',
        'citizenship' => 'Staatsangehörigkeit',
        'family_status' => 'Familienstand',

        // Mitgliedschaft
        'type' => 'Typ',
        'fee_type' => 'Beitragstyp',
        'entered_at' => 'Eingetreten',
        'left_at' => 'Ausgetreten',
        'applied_at' => 'Beantragt',
        'verified_at' => 'E-Mail-Bestätigung',
        'is_deducted' => 'Beitragsbefreiung',
        'deduction_reason' => 'Befreiungsgrund',

        // Rollen
        'roles' => 'Rollen',

        // DSGVO
        'photo_consent_at' => 'Foto-Zustimmung',
        'photo_consent_revoked_at' => 'Foto-Absage',
        'newsletter_consent_at' => 'Newsletter-Zustimmung',
        'newsletter_consent_revoked_at' => 'Newsletter-Absage',
        'gdpr_consent_at' => 'DSGVO-Zustimmung',
        'pseudonymized_at' => 'Pseudonymisiert',
    ];

    public const ENUM_FIELDS = [
        'type' => MemberType::class,
        'fee_type' => MemberFeeType::class,
        'gender' => Gender::class,
        'family_status' => MemberFamilyStatus::class,
    ];

    /**
     * Analyse CSV headers against CommuCore fields.
     *
     * @param  string[]  $csvHeaders
     * @return array{
     *     auto_mapped: array<string, string>,
     *     unmapped_csv: string[],
     *     unmapped_commucore: string[],
     *     needs_manual_mapping: bool,
     * }
     */
    public static function analyse(array $csvHeaders): array
    {
        $commuCoreFields = array_keys(self::MEMBER_FIELDS);

        // Invertierte Label-Map: deutsches Label → DB-Feldname
        $labelToField = array_flip(self::MEMBER_FIELDS);
        // z.B. 'Name (Nachname)' => 'name', 'Vorname' => 'first_name'

        $autoMapped = [];
        $unmappedCsv = [];

        foreach ($csvHeaders as $header) {
            if (in_array($header, $commuCoreFields, strict: true)) {
                // Exakter DB-Feldname Match: 'name' → 'name'
                $autoMapped[$header] = $header;
            } elseif (isset($labelToField[$header])) {
                // Label Match: 'Vorname' → 'first_name'
                $autoMapped[$header] = $labelToField[$header];
            } else {
                $unmappedCsv[] = $header;
            }
        }

        $unmappedCommuCore = array_values(array_diff(
            $commuCoreFields,
            array_values($autoMapped),
        ));

        return [
            'auto_mapped' => $autoMapped,
            'unmapped_csv' => $unmappedCsv,
            'unmapped_commucore' => $unmappedCommuCore,
            'needs_manual_mapping' => $unmappedCsv !== [],
        ];
    }

    /**
     * Apply field mapping to a single row.
     *
     * @param  array<string, string>  $row  Raw CSV row
     * @param  array<string, string>  $fieldMap  csvHeader → commuCoreField
     * @return array<string, string>
     */
    public static function applyMapping(array $row, array $fieldMap): array
    {
        $mapped = [];

        foreach ($fieldMap as $csvHeader => $commuCoreField) {
            if (array_key_exists($csvHeader, $row)) {
                $mapped[$commuCoreField] = $row[$csvHeader];
            }
        }

        return $mapped;
    }

    /**
     * Detect unknown Enum values in parsed rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @param  array<string, string>  $fieldMap  csvHeader → commuCoreField
     * @return array<string, string[]> field → list of unknown values
     */
    public static function detectUnknownEnumValues(array $rows, array $fieldMap): array
    {
        $unknowns = [];

        // Invert fieldMap: commuCoreField → csvHeader
        $invertedMap = array_flip($fieldMap);

        foreach (self::ENUM_FIELDS as $field => $enumClass) {
            $csvHeader = $invertedMap[$field] ?? $field;
            /** @var class-string<\BackedEnum&\App\Enums\Contracts\HasLabel> $enumClass */
            $validValues = array_column($enumClass::cases(), 'value');
            $foundUnknown = [];

            foreach ($rows as $row) {
                $value = trim($row[$csvHeader] ?? '');

                if ($value === '') {
                    continue;
                }

                if (
                    ! in_array($value, $validValues, strict: true)
                    && ! in_array($value, $foundUnknown, strict: true)
                ) {
                    $foundUnknown[] = $value;
                }
            }

            if ($foundUnknown !== []) {
                $unknowns[$field] = $foundUnknown;
            }
        }

        return $unknowns;
    }

    /**
     * Valid values for a given Enum field (for dropdown in mapping modal).
     *
     * @return array<string, string> value => label
     */
    public static function enumOptions(string $field): array
    {
        $enumClass = self::ENUM_FIELDS[$field] ?? null;

        if ($enumClass === null) {
            return [];
        }

        /** @var class-string<\BackedEnum&\App\Enums\Contracts\HasLabel> $enumClass */
        return collect($enumClass::cases())
            ->mapWithKeys(static fn (\BackedEnum&\App\Enums\Contracts\HasLabel $case): array => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }

    /**
     * Apply enum value overrides to all rows.
     *
     * @param  array<int, array<string, string>>  $rows
     * @param  array<string, array<string, string>>  $enumMap  field → [unknownValue => commuCoreValue]
     * @return array<int, array<string, string>>
     */
    public static function applyEnumMapping(array $rows, array $enumMap): array
    {
        return array_map(static function (array $row) use ($enumMap): array {
            foreach ($enumMap as $field => $valueMap) {
                if (isset($row[$field]) && isset($valueMap[$row[$field]])) {
                    $row[$field] = $valueMap[$row[$field]];
                }
            }

            return $row;
        }, $rows);
    }
}
