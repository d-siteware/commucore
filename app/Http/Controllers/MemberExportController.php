<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ExportType;
use App\Services\Export\MemberCsvExporter;
use App\Services\Export\MemberExportQuery;
use App\Services\Export\MemberFullExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MemberExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        Gate::authorize('export', \App\Models\Membership\Member::class);

        $exportType = ExportType::from($request->string('export_type', ExportType::STAMMDATEN->value)->toString());

        /** @var array{include_pseudonymized?: bool, only_active?: bool, member_types?: string[]} $filters */
        $filters = [
            'include_pseudonymized' => $request->boolean('include_pseudonymized'),
            'only_active' => $request->boolean('only_active'),
            'member_types' => $request->array('member_types'),
        ];

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Membership\Member> $members */
        $members = MemberExportQuery::build($filters)->get();

        Log::info('member.export', [
            'user_id' => $request->user()?->id,
            'export_type' => $exportType->value,
            'filters' => $filters,
            'count' => $members->count(),
        ]);

        if ($exportType === ExportType::FULL) {
            return $this->zipResponse($members);
        }

        return $this->csvResponse($members, $exportType);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Membership\Member>  $members
     */
    private function csvResponse(
        \Illuminate\Database\Eloquent\Collection $members,
        ExportType $type,
    ): StreamedResponse {
        $filename = sprintf('members_%s_%s.csv', $type->value, now()->format('Y-m-d'));

        return response()->streamDownload(function () use ($members, $type): void {
            $stream = MemberCsvExporter::toStream($members, $type);
            fpassthru($stream);
            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Membership\Member>  $members
     */
    private function zipResponse(
        \Illuminate\Database\Eloquent\Collection $members,
    ): StreamedResponse {
        $zipPath = MemberFullExporter::toZip($members);
        $filename = sprintf('members_full_%s.zip', now()->format('Y-m-d'));

        return response()->streamDownload(function () use ($zipPath): void {
            readfile($zipPath);
            @unlink($zipPath); // Temp-Datei nach dem Stream löschen
        }, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }
}
