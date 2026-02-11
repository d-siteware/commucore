<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Actions\Accounting\CreateMemberTransaction;
use App\Enums\AccountType;
use App\Enums\EventStatus;
use App\Enums\Gender;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class TransactionSeeder extends Seeder
{
    private Account $bank;

    public function run(): void
    {
        // deterministischer Zufall
        mt_srand(crc32(config('app.key')));

        $this->bank = Account::whereIn('type', [
            AccountType::paypal->value,
            AccountType::bank->value,
        ])->inRandomOrder()->first();

        foreach ($this->months() as $month) {
            $this->seedFixCosts($month);
            $this->seedMembershipFees($month);
            $this->seedEvents($month);
        }
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

    private function seedFixCosts(Carbon $month): void
    {
        $this->expense(
            'Hosting & Software'.$month->translatedFormat('F Y'),
            rand(2000, 3500),
            $month->copy()->addDay(1),
            10
        );

        $this->expense(
            'Raummiete'.$month->translatedFormat('F Y'),
            rand(8000, 12000),
            $month->copy()->addDay(3),
            10
        );
    }

    private function seedMembershipFees(Carbon $month): void
    {
        $count = rand(18, 25);

        for ($i = 0; $i < $count; $i++) {
           $member = Member::query()->inRandomOrder()->first();
           $transaction = $this->income(
                'Mitgliedsbeitrag '.' - '.$member->fullName().'/'.$month->translatedFormat('F Y'),
                rand(2000, 3000),
                $month->copy()->addDays(rand(1, 5)),
                13
            );

           MemberTransaction::create([
               'member_id' => $member->id,
               'transaction_id' => $transaction->id,
               'is_membership_fee' => true,
               'fee_year' => now()->year,
           ]);

        }
    }

    private function seedEvents(Carbon $month): void
    {
        $events = rand(1, 2);

        for ($i = 0; $i < $events; $i++) {
            // aus deiner Monatslogik
            $text = DemoClubText::randomEvent();

            $date = $month
                ->copy()
                ->addDays(rand(0, $month->daysInMonth - 1));

            $event = Event::create([
                'name' => $text['title']['de'].' '.$month->translatedFormat('F Y'), // interner Name
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
                'status' => EventStatus::PUBLISHED->value,
                'slug' => $this->localizedSlugs($text['title'], $date),
            ]);

            $visitorCount = rand(15, 25) + $month->diffInMonths(now()) * 3;

            $visitors = EventVisitor::factory()
                ->count((int) $visitorCount)
                ->create([
                    'gender' => Gender::ma,
                    'event_id' => $event->id,
                ]);

            foreach ($visitors as $visitor) {
                $this->ticketTransaction($event, $visitor);
            }

            $this->eventExpenses($event);
        }
    }

    private function ticketTransaction(Event $event, EventVisitor $visitor): void
    {
        $gross = rand(1800, 3500); // 18–35 €
        $vatRate = 19;
        $vat = (int) round($gross * ($vatRate / 119));

        $transaction = Transaction::create([
            'date' => $event->event_date,
            'label' => 'Veranstaltungsticket: '.$event->title['de'],
            'description' => 'Ticket für '.$visitor->name,
            'amount_gross' => $gross,
            'vat' => $vatRate,
            'tax' => $vat,
            'amount_net' => $gross - $vat,
            'account_id' => $this->bank->id,
            'booking_account_id' => 2,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        EventTransaction::create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
        ]);
    }

    private function eventExpenses(Event $event): void
    {
        $this->expense(
            'Catering – '.$event->title['de'],
            rand(3000, 8000),
            $event->event_date,
            7
        );

        $this->expense(
            'Technik – '.$event->title['de'],
            rand(2000, 6000),
            $event->event_date,
            9
        );
    }

    private function income(string $label, int $gross, Carbon $date, $booking_account_id = 12): Transaction
    {
      return  $this->createTransaction(
            $label,
            $gross,
            $date,
            TransactionType::Deposit,
            $this->bank->id,
            $booking_account_id
        );
    }

    private function expense(string $label, int $gross, Carbon $date, $booking_account_id = 10): Transaction
    {
      return   $this->createTransaction(
            $label,
            $gross,
            $date,
            TransactionType::Withdrawal,
            $this->bank->id,
            $booking_account_id
        );
    }

    private function createTransaction(
        string $label,
        int $gross,
        Carbon $date,
        TransactionType $type,
        int $accountId,
        int $booking_account_id
    ): Transaction {
        $vatRate = 19;
        $vat = (int) round($gross * ($vatRate / 119));

       return Transaction::create([
            'date' => $date,
            'label' => $label,
            'amount_gross' => $gross,
            'vat' => $vatRate,
            'tax' => $vat,
            'amount_net' => $gross - $vat,
            'account_id' => $accountId,
            'type' => $type,
            'booking_account_id' => $booking_account_id,
            'status' => TransactionStatus::booked,
        ]);
    }
}
