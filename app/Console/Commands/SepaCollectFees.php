<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FeeService;
use App\Services\Sepa\SepaCollectionService;
use App\Services\Sepa\SepaSettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

final class SepaCollectFees extends Command
{
    protected $signature = 'commucore:collect-sepa-fees
        {--date= : Referenzdatum (Y-m-d, default: heute)}
        {--dry-run : Nur Vorschau, keine XML-Generierung}
        {--store : XML in storage speichern statt Stream-Download}';

    protected $description = 'Erzeugt SEPA-Batch-XML für alle offenen Beitragszahlungen mit aktivem Mandat';

    public function handle(
        SepaCollectionService $collectionService,
        SepaSettingsService $sepaSettings,
        FeeService $feeService,
    ): int {
        $inputDate = $this->option('date');
        $referenceDate = $inputDate
            ? Carbon::createFromFormat('Y-m-d', $inputDate)
            : now();

        $dryRun = (bool) $this->option('dry-run');
        $store = (bool) $this->option('store');

        if (! $sepaSettings->isConfigured()) {
            $this->components->error('SEPA-Einstellungen sind nicht konfiguriert (Gläubiger-ID und Konto fehlen).');

            return self::FAILURE;
        }

        try {
            $candidates = $collectionService->findOpenCandidates($referenceDate);
        } catch (\RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($candidates->isEmpty()) {
            $this->components->warn("Keine offenen Beitragszahlungen für {$referenceDate->format('Y-m-d')} – alle Mitglieder haben bereits Transaktionen oder eine ausstehende Einreichung.");

            return self::SUCCESS;
        }

        $this->components->twoColumnDetail('Offene Zahlungen', (string) $candidates->count());

        $totalAmount = 0;

        foreach ($candidates as $member) {
            $amount = $feeService->getAmountForMember($member);
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

        try {
            $result = $collectionService->createAttemptsAndGenerateXml(
                members: $candidates,
                referenceDate: $referenceDate,
            );
        } catch (\RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($result['xml'] === null) {
            $this->components->warn('XML-Generierung fehlgeschlagen.');

            return self::FAILURE;
        }

        if ($result['validation'] && ! $result['validation']->valid) {
            $this->components->error('XML-Validierung fehlgeschlagen, Vorgang abgebrochen:');
            $this->components->error($result['validation']->summary());

            return self::FAILURE;
        }

        $this->components->info($result['validation']?->summary() ?? 'Validierung übersprungen.');
        $this->components->info(count($result['attempts']).' Einreichung(en) angelegt.');

        $filename = 'SEPA-Batch-'.$referenceDate->format('Y-m-d').'-'.now()->format('YmdHis').'.xml';

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
