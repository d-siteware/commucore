<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

final class CsvExportService
{
    public static function exportMembershipFees(Collection $memberPayments, int $year): string
    {
        $filename = "Mitgliedsbeitraege-{$year}-".now()->format('Ymd').'.csv';

        $headers = [
            'Mitglied',
            'Vorname',
            'Nachname',
            'Typ',
            'Letzte Zahlung',
            'Anzahl Zahlungen',
            'Bezahlt (EUR)',
            'Offen (EUR)',
            'Gesamt (EUR)',
            'Status',
        ];

        $rows = $memberPayments->map(function ($item): array {
            return [
                $item->member?->fullName() ?? '—',
                $item->member?->first_name,
                $item->member?->name,
                $item->member?->type,
                \App\Helpers\DateHelper::formatDate($item->latest_transaction?->transaction?->date) ?: '',
                $item->transaction_count,
                \App\Helpers\MoneyHelper::formatCents($item->total_paid, withSymbol: false),
                \App\Helpers\MoneyHelper::formatCents($item->total_pending, withSymbol: false),
                \App\Helpers\MoneyHelper::formatCents($item->total_amount, withSymbol: false),
                $item->has_paid ? 'gebucht' : 'eingereicht',
            ];
        })->toArray();

        // CSV erstellen
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM für Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header schreiben
        fputcsv($output, $headers, ';');

        // Daten schreiben
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        // Gesamtsummen
        fputcsv($output, [], ';');
        fputcsv($output, [
            'GESAMT',
            '',
            '',
            '',
            '',
            $memberPayments->count(),
            \App\Helpers\MoneyHelper::formatCents($memberPayments->sum('total_paid'), withSymbol: false),
            \App\Helpers\MoneyHelper::formatCents($memberPayments->sum('total_pending'), withSymbol: false),
            \App\Helpers\MoneyHelper::formatCents($memberPayments->sum('total_amount'), withSymbol: false),
            '',
        ], ';');

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
