<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Models\Membership\Member;
use Illuminate\Support\Facades\Storage;

/**
 * Creates a JSON backup of the members table (and pivot) before import.
 *
 * Backup is stored in storage/app/imports/backup_{timestamp}_{uuid}.json
 * and can be used to roll back a failed import.
 *
 * Retention: backups older than 24 hours should be pruned via a scheduled command.
 */
final class MemberImportBackup
{
    private const DISK = 'local';

    private const DIRECTORY = 'imports';

    /**
     * Create a backup and return the storage path.
     */
    public static function create(int $userId): string
    {
        $payload = [
            'backup_at' => now()->toIso8601String(),
            'imported_by_user_id' => $userId,
            'app_version' => config('app.version', '1.0'),
            'tables' => [
                'members' => self::dumpMembers(),
                'member_role' => self::dumpMemberRoles(),
            ],
        ];

        $filename = sprintf(
            '%s/backup_%s_%s.json',
            self::DIRECTORY,
            now()->format('Y-m-d_His'),
            str()->uuid(),
        );

        Storage::disk(self::DISK)
            ->put($filename, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filename;
    }

    /**
     * Restore members and pivot from a backup file.
     *
     * @throws \RuntimeException
     */
    public static function restore(string $backupPath): int
    {
        if (! Storage::disk(self::DISK)
            ->exists($backupPath)) {
            throw new \RuntimeException("Backup file not found: {$backupPath}");
        }

        $content = Storage::disk(self::DISK)
            ->get($backupPath);

        if ($content === null) {
            throw new \RuntimeException("Could not read backup file: {$backupPath}");
        }

        /** @var array{tables: array{members: array<int, array<string, mixed>>, member_role: array<int, array<string, mixed>>}} $payload */
        $payload = json_decode($content, associative: true);

        $restored = 0;

        // SQLite: PRAGMA muss VOR der Transaction gesetzt werden
        match (\DB::getDriverName()) {
            'sqlite' => \DB::statement('PRAGMA foreign_keys = OFF'),
            'mysql' => \DB::statement('SET FOREIGN_KEY_CHECKS=0'),
            'pgsql' => \DB::statement('SET session_replication_role = replica'),
            default => null,
        };

        try {
            \DB::transaction(function () use ($payload, &$restored): void {
                \DB::table('member_role')
                    ->delete();
                \DB::table('members')
                    ->delete();

                foreach (array_chunk($payload['tables']['members'], 100) as $chunk) {
                    \DB::table('members')
                        ->insert($chunk);
                    $restored += count($chunk);
                }

                foreach (array_chunk($payload['tables']['member_role'], 100) as $chunk) {
                    $cleanChunk = array_map(
                        static fn (array $row): array => array_diff_key($row, ['id' => '']),
                        $chunk,
                    );
                    \DB::table('member_role')
                        ->insert($cleanChunk);
                }
            });
        } finally {
            // Immer wieder aktivieren – auch bei Exception
            match (\DB::getDriverName()) {
                'sqlite' => \DB::statement('PRAGMA foreign_keys = ON'),
                'mysql' => \DB::statement('SET FOREIGN_KEY_CHECKS=1'),
                'pgsql' => \DB::statement('SET session_replication_role = DEFAULT'),
                default => null,
            };
        }

        return $restored;
    }

    /**
     * Check if a backup file is still within the 24h rollback window.
     */
    public static function isRollbackAllowed(string $backupPath): bool
    {
        if (! Storage::disk(self::DISK)
            ->exists($backupPath)) {
            return false;
        }

        $lastModified = Storage::disk(self::DISK)
            ->lastModified($backupPath);

        return now()->timestamp - $lastModified < 86400; // 24h
    }

    /**
     * Return a temporary download URL for the backup JSON.
     */
    public static function downloadUrl(string $backupPath): string
    {
        return route('import.backup-download', [
            'path' => encrypt($backupPath), // Pfad verschlüsseln – kein direkter Zugriff
        ]);
    }

    /**
     * Löscht Backups älter als 24h (Retention für Mitglieder-PII).
     *
     * @return int Anzahl gelöschter Dateien
     */
    public static function pruneExpired(): int
    {
        $deleted = 0;

        foreach (Storage::disk(self::DISK)->files(self::DIRECTORY) as $file) {
            if (! str_starts_with(basename($file), 'backup_')) {
                continue;
            }

            $lastModified = Storage::disk(self::DISK)->lastModified($file);

            if (now()->timestamp - $lastModified > 86400) {
                Storage::disk(self::DISK)->delete($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Delete a backup file.
     */
    public static function delete(string $backupPath): void
    {
        if (Storage::disk(self::DISK)
            ->exists($backupPath)) {
            Storage::disk(self::DISK)
                ->delete($backupPath);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function dumpMembers(): array
    {
        return Member::query()
            ->get()
            ->map(static fn (Member $m): array => $m->getAttributes())
            ->toArray();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function dumpMemberRoles(): array
    {
        return \DB::table('member_role')
            ->get()
            ->map(static fn (object $row): array => (array) $row)
            ->toArray();
    }
}
