<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\FundingStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Project\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class FundingSeeder extends Seeder
{
    private Account $bank;

    public function run(): void
    {
        mt_srand(crc32(config('app.key')));

        $this->bank = Account::whereIn('type', ['paypal', 'bank'])->inRandomOrder()->first()
            ?? Account::first();

        $currentYear = (int) now()->format('Y');
        $lastYear    = $currentYear - 1;

        // Projekte holen – müssen nach ProjectSeeder existieren
        $renovation = Project::where('title', 'like', 'Vereinsheim-Renovierung%')->first();
        $youth      = Project::where('title', 'like', 'Jugendprogramm%')->first();
        $jubilee    = Project::where('title', 'like', '25-Jahr-Jubiläum%')->first();

        // -----------------------------------------------------------------
        // Förderung 1 – Stadtförderung Vereinsheim (abgeschlossen)
        // Bezuschussung der Renovierung durch die Stadtverwaltung
        // -----------------------------------------------------------------
        $stadtfoerderung = Funding::create([
            'title'                 => 'Stadtförderung Vereinsheim '.$lastYear,
            'funder'                => 'Stadt München – Kulturreferat',
            'reference'             => 'KR-'.($lastYear).'-0472',
            'description'           => 'Förderung von Renovierungsmaßnahmen gemeinnütziger Vereinsheime gemäß Vereinsförderrichtlinien der Landeshauptstadt München.',
            'status'                => FundingStatus::Completed,
            'approved_amount'       => 2500_00, // 2.500 €
            'funding_period_start'  => "{$lastYear}-01-01",
            'funding_period_end'    => "{$lastYear}-12-31",
        ]);

        // Zwei Tranchen erhalten
        $this->fundingIncome($stadtfoerderung, 'Stadtförderung Vereinsheim – 1. Tranche', 1500_00, "{$lastYear}-04-01");
        $this->fundingIncome($stadtfoerderung, 'Stadtförderung Vereinsheim – 2. Tranche', 1000_00, "{$lastYear}-07-15");

        // Verknüpfung mit Renovierungsprojekt
        if ($renovation) {
            $stadtfoerderung->projects()->attach($renovation->id, [
                'allocated_amount' => 2500_00,
            ]);
        }

        // -----------------------------------------------------------------
        // Förderung 2 – Landesjugendförderung (aktiv, läuft noch)
        // -----------------------------------------------------------------
        $jugendfoerderung = Funding::create([
            'title'                 => 'Landesjugendförderung '.$lastYear.'/'.$currentYear,
            'funder'                => 'Bayerischer Jugendring (BJR)',
            'reference'             => 'BJR-JFP-'.substr((string) $lastYear, 2).'-1138',
            'description'           => 'Förderung von Jugendprogrammen und außerschulischer Bildungsarbeit gemäß § 12 SGB VIII.',
            'status'                => FundingStatus::Active,
            'approved_amount'       => 1800_00, // 1.800 €
            'funding_period_start'  => "{$lastYear}-09-01",
            'funding_period_end'    => "{$currentYear}-08-31",
        ]);

        // Erste Tranche bereits erhalten
        $this->fundingIncome($jugendfoerderung, 'BJR Jugendförderung – Bewilligungsbescheid Abschlag', 900_00, "{$lastYear}-09-15");

        // Verknüpfung mit Jugendprogramm (Teilfinanzierung)
        if ($youth) {
            $jugendfoerderung->projects()->attach($youth->id, [
                'allocated_amount' => 1200_00, // nur Teilbetrag des Projekts wird gefördert
            ]);
        }

        // -----------------------------------------------------------------
        // Förderung 3 – Kulturfonds Jubiläum (bewilligt, noch kein Geld erhalten)
        // -----------------------------------------------------------------
        $kulturfonds = Funding::create([
            'title'                 => 'Kulturfonds Jubiläumsveranstaltung '.$currentYear,
            'funder'                => 'Bezirk Oberbayern – Kulturabteilung',
            'reference'             => 'BezOBB-K-'.($currentYear).'-0089',
            'description'           => 'Förderung kultureller Jubiläumsveranstaltungen von Vereinen mit mindestens 20-jähriger Geschichte.',
            'status'                => FundingStatus::Active,
            'approved_amount'       => 1200_00, // 1.200 €
            'funding_period_start'  => "{$currentYear}-01-01",
            'funding_period_end'    => "{$currentYear}-12-31",
        ]);

        // Noch kein Geld erhalten – Bescheid liegt vor, Auszahlung nach Verwendungsnachweis
        // Verknüpfung mit Jubiläumsprojekt
        if ($jubilee) {
            $kulturfonds->projects()->attach($jubilee->id, [
                'allocated_amount' => 1200_00,
            ]);
        }

        // -----------------------------------------------------------------
        // Förderung 4 – Bundesfreiwilligendienst-Zuschuss (abgelaufen)
        // Kleiner historischer Datensatz aus Vorvorjahr
        // -----------------------------------------------------------------
        $bfd = Funding::create([
            'title'                 => 'BFD-Zuschuss Vereinsarbeit',
            'funder'                => 'Bundesamt für Familie und zivilgesellschaftliche Aufgaben (BAFzA)',
            'reference'             => 'BAFzA-BFD-'.($lastYear - 1).'-7712',
            'description'           => 'Zuschuss für die Begleitung eines Bundesfreiwilligendienstleistenden im Verein.',
            'status'                => FundingStatus::Completed,
            'approved_amount'       => 480_00, // 480 €
            'funding_period_start'  => ($lastYear - 1).'-01-01',
            'funding_period_end'    => ($lastYear - 1).'-12-31',
        ]);

        $this->fundingIncome($bfd, 'BAFzA BFD-Zuschuss – Jahrespauschale', 480_00, ($lastYear - 1).'-06-01');
        // Kein Projekt verknüpft – wurde direkt für laufende Vereinsarbeit genutzt

        $this->command->getOutput()->writeln(
            '  <info>FundingSeeder:</info> 4 Förderungen mit '.FundingTransaction::count().' Zahlungseingängen angelegt.'
        );
    }

    private function fundingIncome(
        Funding $funding,
        string $label,
        int $gross,
        string $date,
    ): void {
        $vatRate = 0; // Fördergelder sind i.d.R. nicht umsatzsteuerpflichtig
        $tx = Transaction::create([
            'date'               => Carbon::parse($date),
            'label'              => $label,
            'amount_gross'       => $gross,
            'vat'                => $vatRate,
            'amount_net'         => $gross,
            'account_id'         => $this->bank->id,
            'booking_account_id' => 12, // Sonstige Einnahmen
            'type'               => TransactionType::Deposit,
            'status'             => TransactionStatus::booked,
        ]);

        FundingTransaction::create([
            'funding_id'      => $funding->id,
            'transaction_id'  => $tx->id,
            'allocated_amount' => null, // voller Betrag
        ]);
    }
}