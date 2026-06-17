<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SepaMandateStatus;
use App\Enums\TransactionStatus;
use App\Models\Membership\MemberTransaction;
use App\Services\Sepa\SepaCollectionService;
use App\Services\Sepa\SepaDirectDebitService;
use App\Services\Sepa\SepaSettingsService;
use App\Services\Sepa\SepaXmlValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class SepaCollectFees extends Command
{
    protected $signature = 'commucore:collect-sepa-fees
        {--year= : Beitragsjahr (default: aktuelles Jahr)}
        {--dry-run : Nur Vorschau, keine XML-Generierung}
        {--store : XML in storage speichern statt Stream-Download}
        {--create-transactions : Offene Beitrags-Transaktionen für Mitglieder ohne existing MemberTransaction anlegen}
        {--ebics-upload : XML via EBICS an die Bank übermitteln (impliziert --create-transactions)}';

    protected $description = 'Erzeugt SEPA-Batch-XML für alle offenen Beitragszahlungen mit aktivem Mandat';

    public function handle(
        SepaCollectionService $collectionService,
        SepaDirectDebitService $sepaService,
        SepaSettingsService $sepaSettings,
        SepaXmlValidator $xmlValidator,
    ): int {
        $year = (int) ($this->option('year') ?? now()->year);
        $dryRun = (bool) $this->option('dry-run');
        $store = (bool) $this->option('store');
        $createTransactions = (bool) $this->option('create-transactions');
        $ebicsUpload = (bool) $this->option('ebics-upload');

        if (!$sepaSettings->isConfigured()) {
            $this->components->error('SEPA-Einstellungen sind nicht konfiguriert (Gläubiger-ID und Konto fehlen).');

            return self::FAILURE;
        }

        $creditorAccount = $sepaSettings->creditorAccount();

        if (!$creditorAccount) {
            $this->components->error('SEPA-Gläubigerkonto wurde nicht gefunden.');

            return self::FAILURE;
        }

        if ($ebicsUpload) {
            $createTransactions = true;

            if (!$sepaSettings->isEbicsConfigured()) {
                $this->components->error('EBICS ist nicht konfiguriert. Bitte zuerst commucore:ebics-setup ausführen.');

                return self::FAILURE;
            }
        }

        if ($createTransactions) {
            $this->components->info('Lege offene Beitrags-Transaktionen an…');

            $memberTransactions = $collectionService->createFeeTransactions($year);

            if ($memberTransactions->isEmpty()) {
                $this->components->warn("Keine neuen offenen Beitragszahlungen für {$year} – alle Mitglieder haben bereits Transaktionen.");

                if (!$ebicsUpload) {
                    return self::SUCCESS;
                }
            } else {
                $this->components->info(count($memberTransactions).' Beitrags-Transaktion(en) angelegt.');
            }
        }

        $pendingTransactions = MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->where('fee_year', $year)
            ->whereHas('transaction', fn ($q) => $q->where('status', TransactionStatus::submitted))
            ->whereHas('member', fn ($q) => $q->whereHas('sepaMandates', fn ($sq) => $sq
                ->where('status', SepaMandateStatus::Active)
                ->whereNull('payment_completed_at')
            ))
            ->with(['member.activeSepaMandate', 'transaction'])
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->components->warn("Keine offenen Beitragszahlungen für {$year} mit aktivem SEPA-Mandat gefunden.");

            return self::SUCCESS;
        }

        $this->components->twoColumnDetail('Offene Zahlungen', (string) $pendingTransactions->count());

        $totalAmount = 0;
        $transactions = [];

        foreach ($pendingTransactions as $mt) {
            $mandate = $mt->member->activeSepaMandate->first();
            $amount = $mt->transaction->amount_net;
            $totalAmount += $amount;

            $label = $mt->member->fullName().' ('.$mt->member->email.')';
            $amountStr = number_format($amount / 100, 2, ',', '.').' €';
            $this->components->twoColumnDetail($label, $amountStr);

            $transactions[] = [
                'member' => $mt->member,
                'mandate' => $mandate,
                'amount' => $amount,
                'remittanceInformation' => 'Mitgliedsbeitrag '.$mt->fee_year.' - '.$mt->member->fullName(),
                'endToEndId' => 'E2E-'.$mt->member->id.'-'.$mt->fee_year,
            ];
        }

        $totalStr = number_format($totalAmount / 100, 2, ',', '.').' €';
        $this->components->twoColumnDetail('Gesamtsumme', $totalStr);

        if ($dryRun) {
            $this->components->info('Dry-Run abgeschlossen. Keine XML-Datei erzeugt.');

            return self::SUCCESS;
        }

        $xml = $sepaService->generateBatch(
            transactions: $transactions,
            creditorAccount: $creditorAccount,
            creditorId: $sepaSettings->creditorId(),
        );

        $painFormat = $sepaSettings->painFormat();
        $validation = $xmlValidator->validate($xml, $painFormat);

        if ($validation->valid) {
            $this->components->info($validation->summary());
        } else {
            $this->components->warn($validation->summary());
        }

        if ($ebicsUpload) {
            $this->components->info('Übermittle XML via EBICS an die Bank…');

            try {
                $collectionService->uploadToEbics($xml);
            } catch (\RuntimeException $e) {
                $this->components->error('EBICS-Upload fehlgeschlagen: '.$e->getMessage());

                return self::FAILURE;
            }

            $this->components->info('EBICS-Upload erfolgreich. Markiere Transaktionen als gebucht…');

            $collectionService->markAsBooked($pendingTransactions);

            $this->components->info('Alle Transaktionen wurden als gebucht markiert.');

            return self::SUCCESS;
        }

        $filename = 'SEPA-Batch-'.$year.'-'.now()->format('YmdHis').'.xml';

        if ($store) {
            $path = 'sepa/batch/'.$filename;
            Storage::disk('local')->put($path, $xml);
            $this->components->info("XML gespeichert unter: storage/app/{$path}");
        } else {
            $this->output->write($xml);
        }

        return self::SUCCESS;
    }
}
