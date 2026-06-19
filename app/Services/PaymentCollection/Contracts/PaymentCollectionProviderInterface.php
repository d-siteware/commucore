<?php

declare(strict_types=1);

namespace App\Services\PaymentCollection\Contracts;

use App\Models\Membership\Member;

interface PaymentCollectionProviderInterface
{
    /**
     * Legt ein Einzugsmandat beim Anbieter an und liefert dessen anbieterseitige Referenz zurück.
     */
    public function createMandate(Member $member, string $iban, string $accountHolder): string;

    /**
     * Reicht einen Einzug für ein bestehendes Mandat ein. Gibt die anbieterseitige Referenz
     * für diesen einzelnen Einzug zurück (zum späteren Status-Abruf).
     */
    public function submitCollection(string $providerMandateReference, int $amountInCents, string $remittanceInformation): string;

    /**
     * Fragt den aktuellen Status eines eingereichten Einzugs ab.
     */
    public function getCollectionStatus(string $providerCollectionReference): PaymentCollectionStatus;

    /**
     * Widerruft ein bestehendes Mandat beim Anbieter.
     */
    public function cancelMandate(string $providerMandateReference): void;
}
