<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

final class CsvExportService
{
    public static function exportMembershipFees(Collection $memberPayments, int $year): string
    {
        $filename = "Mitgliedsbeitraege-{$year}-" . now()->format('Ymd') . '.csv';

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

        $rows = $memberPayments->map(function ($item) {
            return [
                $item->member->full_name,
                $item->member->first_name,
                $item->member->name,
                $item->member->type,
                $item->latest_transaction?->transaction?->date?->format('d.m.Y') ?? '',
                $item->transaction_count,
                number_format($item->total_paid / 100, 2, ',', '.'),
                number_format($item->total_pending / 100, 2, ',', '.'),
                number_format($item->total_amount / 100, 2, ',', '.'),
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
            number_format($memberPayments->sum('total_paid') / 100, 2, ',', '.'),
            number_format($memberPayments->sum('total_pending') / 100, 2, ',', '.'),
            number_format($memberPayments->sum('total_amount') / 100, 2, ',', '.'),
            '',
        ], ';');

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}