<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidIban implements ValidationRule
{
    /**
     * Erwartete Gesamtlänge der IBAN pro Land (ISO 13616).
     * Liste auf die für CommuCore relevanten Länder beschränkt,
     * bei Bedarf einfach ergänzen.
     */
    private const LENGTHS = [
        'DE' => 22,
        'AT' => 20,
        'CH' => 21,
        'LI' => 21,
        'FR' => 27,
        'NL' => 18,
        'BE' => 16,
        'LU' => 20,
        'IT' => 27,
        'ES' => 24,
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $iban = strtoupper(str_replace(' ', '', (string) $value));

        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            $fail(__('sepa.validation.iban_format'));

            return;
        }

        $country = substr($iban, 0, 2);

        if (isset(self::LENGTHS[$country]) && strlen($iban) !== self::LENGTHS[$country]) {
            $fail(__('sepa.validation.iban_length', [
                'country' => $country,
                'length' => self::LENGTHS[$country],
            ]));

            return;
        }

        if (! self::checksumValid($iban)) {
            $fail(__('sepa.validation.iban_checksum'));
        }
    }

    private static function checksumValid(string $iban): bool
    {
        // Schritt 1: erste 4 Zeichen ans Ende verschieben
        $rearranged = substr($iban, 4).substr($iban, 0, 4);

        // Schritt 2: Buchstaben in Zahlen umwandeln (A=10 ... Z=35)
        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char)
                ? (string) (ord($char) - ord('A') + 10)
                : $char;
        }

        // Schritt 3: Modulo 97 über die (potenziell sehr große) Zahl,
        // ziffernweise berechnet statt als Integer/bcmath, damit es
        // ohne Erweiterung funktioniert und keine Überlauf-Probleme gibt.
        $remainder = 0;
        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int) $digit) % 97;
        }

        return $remainder === 1;
    }
}