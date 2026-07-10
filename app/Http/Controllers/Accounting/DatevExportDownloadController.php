<?php

declare(strict_types=1);

namespace App\Http\Controllers\Accounting;

use App\Models\Accounting\DatevExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class DatevExportDownloadController
{
    public function download(Request $request, DatevExport $datevExport)
    {
        if (! $request->hasValidSignature()) {
            abort(401, __('accounting.datev.download.link_expired'));
        }

        if ($datevExport->zip_path === null || ! Storage::disk('local')->exists($datevExport->zip_path)) {
            abort(404, __('accounting.datev.download.not_found'));
        }

        $zipName = 'DATEV_Export_'
            .$datevExport->exported_at->format('Y-m-d')
            .'.zip';

        return Storage::disk('local')->download($datevExport->zip_path, $zipName, [
            'X-Archive-Hash-Sha256' => $datevExport->zip_hash,
        ]);
    }
}
