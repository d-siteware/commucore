<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Imagick;

final class DocumentController extends Controller
{
    // =========================================================================
    // Download
    // =========================================================================

    public function download(Request $request, string $uuid): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $document = Document::where('uuid', $uuid)->firstOrFail();

        if (! Auth::user()->can('view', $document->documentable)) {
            abort(403);
        }

        $path = storage_path('app/private/'.$document->path);

        if (! file_exists($path)) {
            abort(404, 'Datei nicht gefunden.');
        }

        $document->recordAccess($request->user());

        return FacadeResponse::download($path, $document->original_name, [
            'Content-Type' => $document->mime_type,
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    // =========================================================================
    // Preview (inline – für <img> Tags)
    // =========================================================================

    public function preview(Request $request, string $uuid): Response
    {
        if (! app()->isProduction()) {
            putenv('PATH='.getenv('PATH').':/opt/homebrew/bin');
        }

        $document = Document::where('uuid', $uuid)->firstOrFail();

        if (! Auth::user()->can('view', $document->documentable)) {
            abort(403);
        }

        $path = storage_path('app/private/'.$document->path);

        if (! file_exists($path)) {
            Log::error("Document preview: Datei nicht gefunden: {$path}");
            abort(404, 'Datei nicht gefunden.');
        }

        // Bilder direkt ausliefern
        if (in_array($document->mime_type, ['image/jpeg', 'image/png', 'image/tiff', 'image/webp'], strict: true)) {
            return response()->file($path, [
                'Content-Type' => $document->mime_type,
                'Cache-Control' => 'private, max-age=300',
            ]);
        }

        // PDF → erste Seite als PNG via Imagick
        if ($document->mime_type === 'application/pdf') {
            try {
                $imagick = new Imagick;
                Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 512);
                $imagick->setOption('density', '99');
                $imagick->setOption('antialias', 'true');
                $imagick->setOption('pdf:use-cropbox', 'true');
                $imagick->setResolution(96, 96);
                $imagick->readImage($path.'[0]');
                $imagick->setImageFormat('png');
                $imagick->stripImage();

                return response($imagick->getImageBlob(), 200, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'private, max-age=300',
                ]);
            } catch (\Exception $e) {
                Log::error('Document preview: PDF-Vorschau fehlgeschlagen', [
                    'uuid' => $uuid,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);

                abort(500, 'Fehler beim Generieren der Vorschau.');
            }
        }

        // Alle anderen Typen (Word, Excel, eml) – kein Preview möglich
        abort(415, 'Nicht unterstütztes Format.');
    }
}
