<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Sepa\EbicsService;
use Illuminate\Console\Command;

final class SepaEbicsSetup extends Command
{
    protected $signature = 'commucore:ebics-setup
        {--force : Bereits initialisierte Schlüssel überschreiben}
        {--hpb-only : Nur HPB (Bank-Schlüssel herunterladen)}';

    protected $description = 'Initialisiert die EBICS-Verbindung (Schlüssel erzeugen, INI/HIA, Bankbrief)';

    public function handle(EbicsService $ebics): int
    {
        if (!$ebics->isConfigured()) {
            $this->components->error('EBICS ist nicht vollständig konfiguriert. Bitte zuerst die EBICS-Einstellungen hinterlegen.');

            return self::FAILURE;
        }

        if ($this->option('hpb-only')) {
            return $this->runHpb($ebics);
        }

        if ($ebics->isInitialized() && !$this->option('force')) {
            $this->components->warn('EBICS-Schlüssel existieren bereits. Verwende --force zum Überschreiben oder --hpb-only für HPB.');

            return self::FAILURE;
        }

        $this->components->task('EBICS-Schlüssel erzeugen (User Signatures)', function () use ($ebics) {
            $ebics->initialize();

            return true;
        });

        $this->components->task('INI an Bank senden (Signaturschlüssel)', function () use ($ebics) {
            $ebics->sendIni();

            return true;
        });

        $this->components->task('HIA an Bank senden (Authentifizierungs- und Verschlüsselungsschlüssel)', function () use ($ebics) {
            $ebics->sendHia();

            return true;
        });

        $pdfPath = storage_path('app/sepa/ebics/bank-letter.pdf');
        $pdfContent = $ebics->generateBankLetterPdf();
        file_put_contents($pdfPath, $pdfContent);

        $this->components->info("Bankbrief erzeugt: {$pdfPath}");
        $this->components->warn('Bitte den Bankbrief ausdrucken, unterschreiben und an die Bank senden.');
        $this->components->warn('Nach Aktivierung durch die Bank: php artisan commucore:ebics-setup --hpb-only');

        return self::SUCCESS;
    }

    private function runHpb(EbicsService $ebics): int
    {
        if (!$ebics->isInitialized()) {
            $this->components->error('EBICS ist nicht initialisiert. Bitte zuerst commucore:ebics-setup ohne --hpb-only ausführen.');

            return self::FAILURE;
        }

        $this->components->task('HPB – Bank-Schlüssel herunterladen', function () use ($ebics) {
            $ebics->downloadHpb();

            return true;
        });

        $this->components->info('Bank-Schlüssel erfolgreich geladen. EBICS ist bereit für FUL (XML-Upload).');

        return self::SUCCESS;
    }
}
