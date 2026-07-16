<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\ProjectStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\BookingAccountType;
use App\Models\Accounting\Transaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class ProjectSeeder extends Seeder
{
    private Account $bank;

    /** @var array<string, int> */
    private array $bookingAccountIds = [];

    public function run(): void
    {
        mt_srand(crc32(config('app.key')));

        $this->bank = Account::whereIn('type', ['paypal', 'bank'])->inRandomOrder()->first()
            ?? Account::first();

        $this->resolveBookingAccounts();

        $currentYear = (int) now()->format('Y');
        $lastYear = $currentYear - 1;

        // -----------------------------------------------------------------
        // Projekt 1 – Vereinsheim-Renovierung (abgeschlossen, letztes Jahr)
        // -----------------------------------------------------------------
        $renovation = Project::create([
            'title' => 'Vereinsheim-Renovierung '.$lastYear,
            'description' => 'Grundlegende Renovierung des Vereinsheims: neue Bestuhlung, Malerarbeiten und Küchenerneuerung.',
            'status' => ProjectStatus::Completed,
            'start_date' => "{$lastYear}-03-01",
            'end_date' => "{$lastYear}-08-31",
        ]);

        // Ausgaben: Handwerker, Material, Mobiliar
        $this->projectExpense($renovation, 'Malerarbeiten Vereinsheim', 185_00, "{$lastYear}-03-15");
        $this->projectExpense($renovation, 'Neue Bestuhlung (48 Stühle)', 960_00, "{$lastYear}-04-10");
        $this->projectExpense($renovation, 'Küchenzeile & Geräte', 142_00, "{$lastYear}-05-20");
        $this->projectExpense($renovation, 'Elektroinstallation', 580_00, "{$lastYear}-06-05");
        // Einnahmen: Eigenanteil aus Vereinskasse (Spende Mitglieder)
        $this->projectIncome($renovation, 'Mitglieder-Spende Renovierung', 750_00, "{$lastYear}-03-01");

        // -----------------------------------------------------------------
        // Projekt 2 – Jugendprogramm (aktiv, läuft über beide Jahre)
        // -----------------------------------------------------------------
        $youth = Project::create([
            'title' => 'Jugendprogramm '.$lastYear.'/'.$currentYear,
            'description' => 'Regelmäßige Workshops und Ausflüge für Nachwuchsmitglieder unter 18 Jahren.',
            'status' => ProjectStatus::Active,
            'start_date' => "{$lastYear}-09-01",
            'end_date' => "{$currentYear}-12-31",
        ]);

        $this->projectExpense($youth, 'Workshop-Materialien Q4/'.$lastYear, 320_00, "{$lastYear}-10-01");
        $this->projectExpense($youth, 'Ausflug Technikmuseum', 285_00, "{$lastYear}-11-15");
        $this->projectExpense($youth, 'Workshop-Materialien Q1/'.$currentYear, 180_00, "{$currentYear}-02-10");
        $this->projectExpense($youth, 'Kursleiter Honorar Q1/'.$currentYear, 480_00, "{$currentYear}-03-01");
        $this->projectIncome($youth, 'Teilnehmerbeiträge Jugendprogramm', 220_00, "{$lastYear}-09-05");

        // -----------------------------------------------------------------
        // Projekt 3 – Digitalisierung Mitgliederverwaltung (geplant)
        // -----------------------------------------------------------------
        $digitalization = Project::create([
            'title' => 'Digitalisierung Mitgliederverwaltung',
            'description' => 'Einführung einer digitalen Mitgliederverwaltung inkl. Online-Antragsformulare und Beitragsabrechnung.',
            'status' => ProjectStatus::Planned,
            'start_date' => "{$currentYear}-06-01",
            'end_date' => "{$currentYear}-12-31",
        ]);
        // Noch keine Transaktionen – Projekt in Planung

        // -----------------------------------------------------------------
        // Projekt 4 – Jubiläumsfeier 25 Jahre (aktiv, laufendes Jahr)
        // -----------------------------------------------------------------
        $jubilee = Project::create([
            'title' => '25-Jahr-Jubiläum Vereinsfeier',
            'description' => 'Großes Jubiläumsfest zum 25-jährigen Vereinsbestehen mit Festakt, Ausstellung und Abendveranstaltung.',
            'status' => ProjectStatus::Active,
            'start_date' => "{$currentYear}-01-01",
            'end_date' => "{$currentYear}-10-31",
        ]);

        $this->projectExpense($jubilee, 'Festschrift Druckkosten', 385_00, "{$currentYear}-01-20");
        $this->projectExpense($jubilee, 'Veranstaltungstechnik Jubiläum', 720_00, "{$currentYear}-02-15");
        $this->projectExpense($jubilee, 'Catering Jubiläumsfeier (Anzahlung)', 450_00, "{$currentYear}-03-01");
        $this->projectIncome($jubilee, 'Ticketvorverkauf Jubiläumsfeier', 315_00, "{$currentYear}-02-01");
        $this->projectIncome($jubilee, 'Sponsoring Lokalzeitung', 500_00, "{$currentYear}-01-15");

        $this->command->getOutput()->writeln(
            '  <info>ProjectSeeder:</info> 4 Projekte mit '.ProjectTransaction::count().' Transaktionsverknüpfungen angelegt.'
        );
    }

    private function resolveBookingAccounts(): void
    {
        $typeId = BookingAccountType::where('slug', 'skr42')->value('id');

        // Projektbezogene Buchungskonten – Key = SKR42-Kontonummer
        $numbers = [
            '69000', // Sonstige betriebliche Aufwendungen (Projektausgaben)
            '40400', // Spenden / Zuwendungen (Mitglieder-Spenden, Teilnehmerbeiträge)
        ];

        $accounts = BookingAccount::whereIn('number', $numbers)
            ->when($typeId !== null, fn ($q) => $q->where('booking_account_type_id', $typeId))
            ->pluck('id', 'number');

        foreach ($numbers as $number) {
            $this->bookingAccountIds[$number] = $accounts[$number]
                ?? throw new \RuntimeException(
                    "BookingAccount {$number} nicht gefunden. SKR42BookingAccountSeeder ausgeführt?"
                );
        }
    }

    private function projectIncome(
        Project $project,
        string $label,
        int $gross,
        string $date,
    ): void {
        $tx = $this->createTransaction($label, $gross, $date, TransactionType::Deposit, $this->bookingAccountIds['40400']);
        ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $tx->id,
            'allocated_amount' => null,
        ]);
    }

    private function projectExpense(
        Project $project,
        string $label,
        int $gross,
        string $date,
    ): void {
        $tx = $this->createTransaction($label, $gross, $date, TransactionType::Withdrawal, $this->bookingAccountIds['69000']);
        ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $tx->id,
            'allocated_amount' => null,
        ]);
    }

    private function createTransaction(
        string $label,
        int $gross,
        string $date,
        TransactionType $type,
        int $bookingAccountId,
    ): Transaction {
        $vatRate = 19;
        $vat = (int) round($gross * ($vatRate / 119));

        return Transaction::create([
            'date' => Carbon::parse($date),
            'label' => $label,
            'amount_gross' => $gross,
            'vat' => $vatRate,
            'amount_net' => $gross - $vat,
            'account_id' => $this->bank->id,
            'booking_account_id' => $bookingAccountId,
            'type' => $type,
            'status' => TransactionStatus::booked,
        ]);
    }
}
