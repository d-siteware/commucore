<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Imports mapped + validated CSV rows into the members table.
 *
 * - Skips duplicates (matched by email)
 * - Chunk-based inserts (50 rows/batch)
 * - Returns a detailed import protocol
 *
 * @phpstan-type ImportRow array<string, string>
 * @phpstan-type ImportProtocol array{
 *     imported: int,
 *     skipped: int,
 *     errors: array<int, array{row: int, reason: string}>,
 *     duration_ms: int,
 * }
 */
final class MemberImporter
{
    private const CHUNK_SIZE = 50;

    /**
     * @param  array<int, ImportRow>  $rows  Already field-mapped rows
     * @param  int  $userId  For audit log
     * @return ImportProtocol
     */
    public static function import(array $rows, int $userId): array
    {
        $start = hrtime(true);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        // Bestehende E-Mails vorladen – verhindert N+1 bei Duplikatsprüfung
        $existingEmails = Member::query()
            ->whereNotNull('email')
            ->pluck('email')
            ->map(static fn (string $e): string => strtolower($e))
            ->flip()
            ->toArray();

        $chunks = array_chunk($rows, self::CHUNK_SIZE, preserve_keys: true);

        foreach ($chunks as $chunk) {
            $toInsert = [];

            foreach ($chunk as $rowIndex => $row) {
                // Duplikatsprüfung
                $email = strtolower(trim($row['email'] ?? ''));

                if ($email !== '' && isset($existingEmails[$email])) {
                    $skipped++;

                    Log::info('member.import.skipped', [
                        'row' => $rowIndex,
                        'email' => $email,
                        'reason' => 'duplicate',
                    ]);

                    continue;
                }

                // Validierung + Casting
                try {
                    $prepared = self::prepareRow($row);
                } catch (\InvalidArgumentException $e) {
                    $errors[] = ['row' => $rowIndex, 'reason' => $e->getMessage()];

                    Log::warning('member.import.error', [
                        'row' => $rowIndex,
                        'reason' => $e->getMessage(),
                    ]);

                    continue;
                }

                $toInsert[] = $prepared;

                // E-Mail als bekannt markieren – verhindert Duplikate innerhalb des Imports
                if ($email !== '') {
                    $existingEmails[$email] = true;
                }
            }

            if ($toInsert !== []) {
                Member::insert($toInsert);
                $imported += count($toInsert);
            }
        }

        $durationMs = (int) round((hrtime(true) - $start) / 1_000_000);

        Log::info('member.import.completed', [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => count($errors),
            'user_id' => $userId,
            'duration_ms' => $durationMs,
        ]);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
            'duration_ms' => $durationMs,
        ];
    }

    /**
     * Cast and validate a single mapped row for DB insert.
     *
     * @param  ImportRow  $row
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    private static function prepareRow(array $row): array
    {
        // Pflichtfeld
        if (empty($row['name'])) {
            throw new \InvalidArgumentException('Pflichtfeld "name" fehlt.');
        }

        // Enum-Werte casten – ungültige Werte fallen auf Standardwert
        $type = MemberType::tryFrom($row['type'] ?? '') ?? MemberType::ST;
        $feeType = MemberFeeType::tryFrom($row['fee_type'] ?? '') ?? MemberFeeType::FULL;

        return [
            'name' => trim($row['name']),
            'first_name' => trim($row['first_name'] ?? '') ?: null,
            'email' => strtolower(trim($row['email'] ?? '')) ?: null,
            'phone' => trim($row['phone'] ?? '') ?: null,
            'mobile' => trim($row['mobile'] ?? '') ?: null,
            'address' => trim($row['address'] ?? '') ?: null,
            'zip' => trim($row['zip'] ?? '') ?: null,
            'city' => trim($row['city'] ?? '') ?: null,
            'country' => trim($row['country'] ?? '') ?: null,
            'locale' => trim($row['locale'] ?? '') ?: null,
            'gender' => trim($row['gender'] ?? '') ?: null,
            'birth_date' => self::parseDate($row['birth_date'] ?? null),
            'birth_place' => trim($row['birth_place'] ?? '') ?: null,
            'citizenship' => trim($row['citizenship'] ?? '') ?: null,
            'family_status' => trim($row['family_status'] ?? '') ?: null,
            'type' => $type->value,
            'fee_type' => $feeType->value,
            'entered_at' => self::parseDate($row['entered_at'] ?? null),
            'left_at' => self::parseDate($row['left_at'] ?? null),
            'applied_at' => self::parseDate($row['applied_at'] ?? null) ?? now()->toDateTimeString(),
            'verified_at' => self::parseDate($row['verified_at'] ?? null) ?? now()->toDateTimeString(),
            'is_deducted' => self::parseBool($row['is_deducted'] ?? '0'),
            'deduction_reason' => trim($row['deduction_reason'] ?? '') ?: null,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
            'photo_consent_at' => self::parseDate($row['photo_consent_at']),
            'photo_consent_revoked_at' => self::parseDate($row['photo_consent_revoked_at']),
            'newsletter_consent_at' => self::parseDate($row['newsletter_consent_at']),
            'newsletter_consent_revoked_at' => self::parseDate($row['newsletter_consent_revoked_at']),
            'gdpr_consent_at' => self::parseDate($row['gdpr_consent_at']),
            'pseudonymized_at' => self::parseDate($row['pseudonymized_at']),
        ];
    }

    private static function parseDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse(trim($value))->toDateTimeString();
        } catch (\Exception) {
            return null;
        }
    }

    private static function parseBool(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['1', 'true', 'ja', 'yes'], strict: true);
    }
}
