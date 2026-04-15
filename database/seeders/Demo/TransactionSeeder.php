<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\AccountType;
use App\Enums\BookingAccountArea;
use App\Enums\EventStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class TransactionSeeder extends Seeder
{
    private Collection $accounts;

    /**
     * Gecachte BookingAccount-IDs, aufgelöst per SKR42-Kontonummer.
     * Vermeidet N+1-Queries im Seeder-Loop.
     *
     * @var array<string, int>
     */
    private array $bookingAccountIds = [];

    public function run(): void
    {
        mt_srand(crc32(config('app.key')));

        $this->accounts = Account::whereIn('type', [
            AccountType::cash->value,
            AccountType::paypal->value,
            AccountType::bank->value,
        ])->get();

        $this->resolveBookingAccounts();

        foreach ($this->months() as $month) {
            $this->seedFixCosts($month);
            $this->seedMembershipFees($month);
            $this->seedEvents($month);
        }
    }

    // =========================================================================
    // Booking Account Resolution
    // =========================================================================

    /**
     * Löst alle benötigten SKR42-Kontonummern in IDs auf.
     * Wirft eine Exception wenn ein Konto fehlt – besser früh scheitern
     * als später mit null-IDs in die DB schreiben.
     */
    private function resolveBookingAccounts(): void
    {
        $numbers = [
            // Ideeller Bereich – Einnahmen
            '50000', // Mitgliedsbeiträge bis 300 €
            '51000', // Spenden gegen Zuwendungsbestätigung
            '52000', // Zuschüsse von Verbänden

            // Zweckbetrieb Sport – Einnahmen
            '70000', // Eintrittsgelder sportliche Veranstaltungen
            '70200', // Kurs- und Lehrgangsgebühren Sport

            // Wirtschaftlicher Geschäftsbetrieb – Einnahmen
            '80000', // Vereinsgaststätte/Bewirtung
            '80400', // Warenverkauf

            // Ideeller Bereich – Aufwendungen
            '41000', // Miete und Pacht
            '41100', // Raumnebenkosten
            '42000', // Büromaterial
            '42100', // Porto und Telefon
            '43000', // Öffentlichkeitsarbeit

            // Zweckbetrieb – Aufwendungen
            '75000', // Sportbedarf
            '75100', // Kosten sportlicher Veranstaltungen

            // Wirtschaftlicher Geschäftsbetrieb – Aufwendungen
            '85000', // Wareneinkauf Gaststätte
        ];

        $accounts = BookingAccount::whereIn('number', $numbers)
            ->pluck('id', 'number');

        foreach ($numbers as $number) {
            if (! $accounts->has($number)) {
                throw new \RuntimeException(
                    "SKR42-Konto {$number} nicht gefunden. SKR42BookingAccountSeeder ausgeführt?"
                );
            }
            $this->bookingAccountIds[$number] = $accounts[$number];
        }
    }

    /**
     * Gibt die DB-ID eines BookingAccounts anhand der SKR42-Kontonummer zurück.
     */
    private function bookingAccountId(string $number): int
    {
        return $this->bookingAccountIds[$number]
            ?? throw new \RuntimeException("BookingAccount {$number} nicht im Cache – resolveBookingAccounts() prüfen.");
    }

    // =========================================================================
    // Account Helpers
    // =========================================================================

    private function randomAccount(): Account
    {
        return $this->accounts->random();
    }

    private function bankAccount(): Account
    {
        return $this->accounts->firstWhere('type', AccountType::bank->value)
            ?? $this->accounts->firstWhere('type', AccountType::cash->value)
            ?? $this->accounts->first();
    }

    private function cashAccount(): Account
    {
        return $this->accounts->firstWhere('type', AccountType::cash->value)
            ?? $this->bankAccount();
    }

    // =========================================================================
    // Month / Slug Helpers
    // =========================================================================

    /**
     * @return Carbon[]
     */
    private function months(): array
    {
        return collect(range(0, 5))
            ->map(fn ($i) => now()->subMonths($i)->startOfMonth())
            ->reverse()
            ->values()
            ->all();
    }

    private function localizedSlugs(array $titles, Carbon $date): array
    {
        return [
            'de' => $this->slugForLocale('de', $titles['de'], $date),
            'hu' => $this->slugForLocale('hu', $titles['hu'], $date),
            'en' => $this->slugForLocale('en', $titles['en'], $date),
        ];
    }

    private function slugForLocale(string $locale, string $title, Carbon $date): string
    {
        return Str::slug(
            $title.' '.$date->locale($locale)->translatedFormat('F Y').' '.$date->day
        );
    }

    // =========================================================================
    // Seeder: Fixkosten
    // =========================================================================

    private function seedFixCosts(Carbon $month): void
    {
        // Hosting & Software → ideeller Bereich, Büromaterial/IT
        // (kein eigenes SKR42-IT-Konto → 42000 Büromaterial als nächste Entsprechung)
        $this->expense(
            label: 'Hosting & Software '.$month->translatedFormat('F Y'),
            gross: rand(2000, 3500),
            date: $month->copy()->addDay(1),
            vat: 19,
            bookingAccountNumber: '42000', // Büromaterial / IT-Kosten
            area: BookingAccountArea::IDEAL,
            account: $this->bankAccount(),
        );

        // Raummiete → ideeller Bereich
        $this->expense(
            label: 'Raummiete '.$month->translatedFormat('F Y'),
            gross: rand(8000, 12000),
            date: $month->copy()->addDay(3),
            vat: 0,    // Mietverträge i.d.R. umsatzsteuerfrei (§ 4 Nr. 12 UStG)
            bookingAccountNumber: '41000', // Miete und Pacht
            area: BookingAccountArea::IDEAL,
            account: $this->bankAccount(),
        );
    }

    // =========================================================================
    // Seeder: Mitgliedsbeiträge
    // =========================================================================

    private function seedMembershipFees(Carbon $month): void
    {
        $count = rand(18, 25);

        for ($i = 0; $i < $count; $i++) {
            /** @var Member $member */
            $member = Member::query()->inRandomOrder()->first();

            // Mitgliedsbeiträge: steuerbefreit (§ 4 Nr. 22 UStG / § 19 UStG)
            // Sphäre: ideeller Bereich
            $transaction = $this->income(
                label: 'Mitgliedsbeitrag – '.$member->fullName().' / '.$month->translatedFormat('F Y'),
                gross: rand(2000, 3000),
                date: $month->copy()->addDays(rand(1, 5)),
                vat: 0,
                bookingAccountNumber: '50000', // Echte Mitgliedsbeiträge bis 300 €
                area: BookingAccountArea::IDEAL,
                account: $this->randomAccount(),
            );

            MemberTransaction::create([
                'member_id' => $member->id,
                'transaction_id' => $transaction->id,
                'is_membership_fee' => true,
                'fee_year' => now()->year,
            ]);
        }
    }

    // =========================================================================
    // Seeder: Events
    // =========================================================================

    private function seedEvents(Carbon $month): void
    {
        $eventCount = rand(1, 2);

        for ($i = 0; $i < $eventCount; $i++) {
            $text = DemoClubText::randomEvent();

            $date = $month->copy()->addDays(rand(0, $month->daysInMonth - 1));

            $event = Event::create([
                'name' => $text['title']['de'].' '.$month->translatedFormat('F Y'),
                'event_date' => $date,
                'title' => array_map(
                    fn ($value) => $value.' '.$date->translatedFormat('F Y'),
                    $text['title']
                ),
                'start_time' => '16:00',
                'end_time' => '22:00',
                'entry_fee' => random_int(1000, 5000),
                'entry_fee_discounted' => random_int(500, 2000),
                'venue_id' => Venue::query()->inRandomOrder()->first()?->id,
                'excerpt' => $text['excerpt'],
                'description' => $text['description'],
                'status' => EventStatus::PUBLISHED,
                'slug' => $this->localizedSlugs($text['title'], $date),
            ]);

            $visitorCount = rand(15, 25) + $month->diffInMonths(now()) * 3;

            // Tickets & EventTransactions
            $visitors = EventVisitor::factory()
                ->count((int) $visitorCount)
                ->create(['event_id' => $event->id]);

            foreach ($visitors as $visitor) {
                $this->ticketTransaction($event, $visitor);
            }

            $this->seedEventExpenses($event);
        }
    }

    /**
     * Ticketeinnahmen: Zweckbetrieb Sport (Eintrittsgelder).
     * USt 7% – kulturelle/sportliche Veranstaltungen (§ 12 Abs. 2 Nr. 7 UStG).
     */
    private function ticketTransaction(Event $event, EventVisitor $visitor): Transaction
    {
        $gross = rand(1800, 3500);
        $vatRate = 7;
        $vat = (int) round($gross * ($vatRate / 107));

        $account = $this->randomAccount();

        $transaction = Transaction::create([
            'date' => $event->event_date,
            'label' => 'Veranstaltungsticket: '.$event->title['de'],
            'description' => 'Ticket für '.$visitor->name,
            'amount_gross' => $gross,
            'vat' => $vatRate,
            'amount_net' => $gross - $vat,
            'account_id' => $account->id,
            'booking_account_id' => $this->bookingAccountId('70000'), // Eintrittsgelder Sport
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        EventTransaction::create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
        ]);

        // visitor bekommt transaction_id – kein zweiter factory()-Aufruf nötig
        $visitor->update(['transaction_id' => $transaction->id]);

        return $transaction;
    }

    /**
     * Veranstaltungsausgaben: Zweckbetrieb Sport.
     */
    private function seedEventExpenses(Event $event): void
    {
        // Catering → Zweckbetrieb (Kosten sportlicher Veranstaltungen)
        // Verpflegung bei Sportveranstaltungen 7% (§ 12 Abs. 2 Nr. 1 UStG)
        $this->expense(
            label: 'Catering – '.$event->title['de'],
            gross: rand(3000, 8000),
            date: $event->event_date,
            vat: 7,
            bookingAccountNumber: '75100', // Kosten sportlicher Veranstaltungen
            area: BookingAccountArea::PURPOSE_OPERATION,
            account: $this->cashAccount(),
        );

        // Technik → Zweckbetrieb (Kosten sportlicher Veranstaltungen)
        $this->expense(
            label: 'Technik – '.$event->title['de'],
            gross: rand(2000, 6000),
            date: $event->event_date,
            vat: 19,
            bookingAccountNumber: '75100', // Kosten sportlicher Veranstaltungen
            area: BookingAccountArea::PURPOSE_OPERATION,
            account: $this->bankAccount(),
        );
    }

    // =========================================================================
    // Transaction Factories
    // =========================================================================

    private function income(
        string $label,
        int $gross,
        Carbon $date,
        int $vat,
        string $bookingAccountNumber,
        BookingAccountArea $area,
        ?Account $account = null,
    ): Transaction {
        return $this->createTransaction(
            label: $label,
            gross: $gross,
            date: $date,
            type: TransactionType::Deposit,
            vat: $vat,
            bookingAccountNumber: $bookingAccountNumber,
            area: $area,
            account: $account ?? $this->randomAccount(),
        );
    }

    private function expense(
        string $label,
        int $gross,
        Carbon $date,
        int $vat,
        string $bookingAccountNumber,
        BookingAccountArea $area,
        ?Account $account = null,
    ): Transaction {
        return $this->createTransaction(
            label: $label,
            gross: $gross,
            date: $date,
            type: TransactionType::Withdrawal,
            vat: $vat,
            bookingAccountNumber: $bookingAccountNumber,
            area: $area,
            account: $account ?? $this->bankAccount(),
        );
    }

    private function createTransaction(
        string $label,
        int $gross,
        Carbon $date,
        TransactionType $type,
        int $vat,
        string $bookingAccountNumber,
        BookingAccountArea $area,
        Account $account,
    ): Transaction {
        $vatAmount = $vat > 0
            ? (int) round($gross * ($vat / (100 + $vat)))
            : 0;

        return Transaction::create([
            'date' => $date,
            'label' => $label,
            'amount_gross' => $gross,
            'vat' => $vat,
            'amount_net' => $gross - $vatAmount,
            'account_id' => $account->id,
            'booking_account_id' => $this->bookingAccountId($bookingAccountNumber),
            'type' => $type,
            'status' => TransactionStatus::booked,
            'area' => $area->value,
        ]);
    }
}
