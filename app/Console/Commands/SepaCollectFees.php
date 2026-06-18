<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Sepa\SepaCollectionService;
use App\Services\Sepa\SepaSettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class SepaCollectFees extends Command
{
    protected $signature = 'commucore:collect-sepa-fees
        {--year= : Beitragsjahr (default: aktuelles Jahr)}
        {--dry-run : Nur Vorschau, keine XML-Generierung}
        {--store : XML in storage speichern statt Stream-Download}
        {--ebics-upload : XML via EBICS an die Bank übermitteln (impliziert keine automatische Bestätigung)}';

    protected $description = 'Erzeugt SEPA-Batch-XML für alle offenen Beitragszahlungen mit aktivem Mandat';

    public function handle(
        SepaCollectionService $collectionService,
        SepaSettingsService $sepaSettings,
    ): int {
        $year = (int) ($this->option('year') ?? now()->year);
        $dryRun = (bool) $this->option('dry-run');
        $store = (bool) $this->option('store');
        $ebicsUpload = (bool) $this->option('ebics-upload');

        if (!$sepaSettings->isConfigured()) {
            $this->components->error('SEPA-Einstellungen sind nicht konfiguriert (Gläubiger-ID und Konto fehlen).');

            return self::FAILURE;
        }

        if ($ebicsUpload && !$sepaSettings->isEbicsConfigured()) {
            $this->components->error('EBICS ist nicht konfiguriert. Bitte zuerst commucore:ebics-setup ausführen.');

            return self::FAILURE;
        }

        try {
            $candidates = $collectionService->findOpenCandidates($year);
        } catch (\RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($candidates->isEmpty()) {
            $this->components->warn("Keine offenen Beitragszahlungen für {$year} – alle Mitglieder haben bereits Transaktionen oder ein ausstehende Einreichung.");

            return self::SUCCESS;
        }

        $this->components->twoColumnDetail('Offene Zahlungen', (string) $candidates->count());

        $totalAmount = 0;

        foreach ($candidates as $member) {
            $amount = $member->fee_type->fee() * 12;
            $totalAmount += $amount;

            $label = $member->fullName().' ('.$member->email.')';
            $amountStr = number_format($amount / 100, 2, ',', '.').' €';
            $this->components->twoColumnDetail($label, $amountStr);
        }

        $totalStr = number_format($totalAmount / 100, 2, ',', '.').' €';
        $this->components->twoColumnDetail('Gesamtsumme', $totalStr);

        if ($dryRun) {
            $this->components->info('Dry-Run abgeschlossen. Keine XML-Datei erzeugt.');

            return self::SUCCESS;
        }

        $result = $collectionService->createAttemptsAndGenerateXml(
            members: $candidates,
            year: $year,
        );

        if ($result['xml'] === null) {
            $this->components->warn('XML-Generierung fehlgeschlagen.');

            return self::FAILURE;
        }

        if ($result['validation'] && !$result['validation']->valid) {
            $this->components->error('XML-Validierung fehlgeschlagen, Vorgang abgebrochen:');
            $this->components->error($result['validation']->summary());

            return self::FAILURE;
        }

        $this->components->info($result['validation']?->summary() ?? 'Validierung übersprungen.');
        $this->components->info(count($result['attempts']).' Einreichung(en) angelegt.');

        if ($ebicsUpload) {
            $this->components->info('Übermittle XML via EBICS an die Bank…');

            try {
                $collectionService->uploadToEbics($result['xml']);
            } catch (\RuntimeException $e) {
                $this->components->error('EBICS-Upload fehlgeschlagen: '.$e->getMessage());

                return self::FAILURE;
            }

            $this->components->info('SEPA-XML wurde erfolgreich via EBICS übermittelt.');
            $this->components->warn('Die Einreichungen müssen manuell bestätigt werden (via WebUI oder commucore:confirm-sepa-attempts).');

            return self::SUCCESS;
        }

        $filename = 'SEPA-Batch-'.$year.'-'.now()->format('YmdHis').'.xml';

        if ($store) {
            $path = 'sepa/batch/'.$filename;
            Storage::disk('local')->put($path, $result['xml']);
            $this->components->info("XML gespeichert unter: storage/app/{$path}");
        } else {
            $this->output->write($result['xml']);
        }

        return self::SUCCESS;
    }
}
