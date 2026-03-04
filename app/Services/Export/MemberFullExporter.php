<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Models\Membership\Member;
use App\Models\Membership\MemberDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Generates a ZIP archive containing:
 *  - members_all.csv  (all member data)
 *  - documents/{member_id}_{name}/  (all MemberDocuments per member)
 *
 * The ZIP is written to a temp file and the path is returned.
 * The caller is responsible for streaming and deleting the temp file.
 */
final class MemberFullExporter
{
    /**
     * @param  Collection<int, Member>  $members
     */
    public static function toZip(Collection $members): string
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'commucore_export_').'.zip';

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create ZIP archive.');
        }

        // CSV hinzufügen
        $csvStream = MemberCsvExporter::toStream($members, \App\Enums\ExportType::MEMBERS_ALL);
        $csvContent = stream_get_contents($csvStream);

        if ($csvContent === false) {
            throw new \RuntimeException('Could not read CSV stream.');
        }

        $zip->addFromString('members_all.csv', $csvContent);

        // Dokumente pro Member hinzufügen
        foreach ($members as $member) {
            $documents = MemberDocument::query()
                ->whereMemberId($member->id)
                ->get();

            foreach ($documents as $document) {
                if (! $document->storageExists()) {
                    continue;
                }

                $fileContent = Storage::disk($document->disk)->get($document->path);

                if ($fileContent === null) {
                    continue;
                }

                $folderName = sprintf(
                    'documents/%d_%s',
                    $member->id,
                    str($member->name)->slug()->toString(),
                );

                $zip->addFromString(
                    $folderName.'/'.$document->original_name,
                    $fileContent,
                );
            }
        }

        // Checksum der CSV berechnen
        $csvHash = 'sha256:'.hash('sha256', $csvContent);

        // Manifest erstellen
        $manifest = json_encode([
            'version' => '1.0',
            'app' => 'commucore',
            'exported_at' => now()->toIso8601String(),
            'export_type' => 'full',
            'member_count' => $members->count(),
            'checksums' => [
                'members_all.csv' => $csvHash,
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $zip->addFromString('commucore_export.json', $manifest);

        $zip->close();

        return $zipPath;
    }
}
