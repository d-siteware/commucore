<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Accounting\CreateAccount;
use App\Enums\AccountType;
use Illuminate\Console\Command;

final class MakeAccountCommand extends Command
{
    protected $signature = 'commucore:make-account
        {--name=          : Bezeichnung des Kontos}
        {--number=          : Nummer-ID des Kontos}
        {--type=          : Kontotyp (Barkasse|Bankkonto|PayPal)}
        {--institute=     : Institut / Bank (optional)}
        {--iban=          : IBAN (optional)}
        {--bic=           : BIC (optional)}
        {--starting-amount=0 : Startguthaben in Cent}';

    protected $description = 'Legt ein Zahlungskonto auf der Instanz an.';

    public function handle(): int
    {
        $name = (string) $this->option('name');
        $number = (string) $this->option('number');
        $type = (string) $this->option('type');
        $cents = (int) $this->option('starting-amount');

        if ($name === '') {
            $this->error('--name ist erforderlich.');

            return self::FAILURE;
        }

        // Enum-Wert validieren
        $validTypes = array_column(AccountType::cases(), 'value');
        if (! in_array($type, $validTypes, true)) {
            $this->error("Ungültiger Typ \"{$type}\". Erlaubt: ".implode(', ', $validTypes));

            return self::FAILURE;
        }

        CreateAccount::handle([
            'name' => $name,
            'number' => $number,
            'type' => $type,
            'institute' => (string) $this->option('institute') ?: null,
            'iban' => (string) $this->option('iban') ?: null,
            'bic' => (string) $this->option('bic') ?: null,
            'starting_amount' => $cents,
            // number wird aus name generiert – analog zu CreateAccount
        ]);

        $this->info("Konto \"{$name}\" erfolgreich angelegt.");

        return self::SUCCESS;
    }
}
