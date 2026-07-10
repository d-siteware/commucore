<?php

declare(strict_types=1);

return [
    'export' => [
        'type' => [
            'buchungsstapel' => 'DATEV Buchungsstapel',
            'buchungsstapel_desc' => 'Buchungsdaten im DATEV-Format (EXTF) für den Import beim Steuerberater.',
            'stammdaten' => 'DATEV Kontenbeschriftungen',
            'stammdaten_desc' => 'Sachkonten (SKR42) als DATEV-kompatible Stammdaten-CSV.',
        ],
    ],
    'datev' => [
        'settings' => [
            'tab' => 'DATEV',
            'heading' => 'DATEV-Einstellungen',
            'subheading' => 'Zugangsdaten für den DATEV-Export (Buchungsstapel). Beraternummer und Mandantennummer erhältst du von deinem Steuerberater.',
            'not_configured_heading' => 'DATEV noch nicht konfiguriert',
            'not_configured_text' => 'Solange Beraternummer und Mandantennummer fehlen, enthält der DATEV-Export Platzhalter und kann nicht beim Steuerberater importiert werden.',
            'berater_nr' => 'Beraternummer',
            'berater_nr_description' => '4–7-stellig (1001–9999999), vom Steuerberater',
            'mandant_nr' => 'Mandantennummer',
            'mandant_nr_description' => '1–5-stellig (1–99999), vom Steuerberater',
            'skr' => 'Sachkontenrahmen (SKR)',
            'skr_description' => 'Aktuell wird ausschließlich SKR42 unterstützt – der von DATEV empfohlene Kontenrahmen für Vereine.',
            'skr_42' => 'Vereine, Stiftungen, gemeinnützige GmbH',
            'skr_49' => 'Vereine (Alt, 4-stellig)',
            'konto_laenge' => 'Sachkontenlänge',
            'konto_laenge_description' => 'Wird automatisch vom Kontenrahmen abgeleitet (SKR42 = 5).',
            'application_info' => 'Exportiert von',
            'application_info_description' => 'Erscheint im DATEV-Header (max. 25 Zeichen)',
            'recipient_email' => 'E-Mail für DATEV-Versand',
            'recipient_email_description' => 'DATEV-Exporte können per E-Mail an den Steuerberater gesendet werden.',
            'info' => [
                'heading' => 'Hinweise zum DATEV-Export',
                'numbers_text' => 'Beraternummer und Mandantennummer identifizieren den Mandanten in der DATEV-Kanzleisoftware. Beide Nummern müssen exakt mit den Werten des Steuerberaters übereinstimmen, sonst schlägt der Import fehl.',
                'validation_text' => 'Exportierte Dateien können vor der Übergabe mit dem offiziellen DATEV-Format-Prüfprogramm (developer.datev.de → DATEV-Format → Tools) technisch geprüft werden. Die endgültige Validierung erfolgt beim Testimport in DATEV Rechnungswesen.',
            ],
            'btn' => [
                'save' => 'DATEV-Einstellungen speichern',
            ],
            'toast' => [
                'save_success_heading' => 'Gespeichert',
                'save_success_text' => 'Die DATEV-Einstellungen wurden gespeichert.',
            ],
        ],
        'mail' => [
            'subject' => 'DATEV-Export :period',
            'greeting' => 'Hallo',
            'heading' => 'DATEV-Export für :period',
            'body' => 'anbei der DATEV-Export für das Konto :account im Zeitraum :period.',
            'zip_structure' => 'Das ZIP-Archiv enthält die Buchungs-CSV sowie die zugehörigen Belege, sortiert in <b>Eingang/</b> (Einnahmen), <b>Ausgang/</b> (Ausgaben) und <b>Kasse/</b> (Barzahlungen).',
            'download_link_label' => 'DATEV-Export herunterladen',
            'link_expiry' => 'Der Download-Link ist 7 Tage gültig.',
            'checksum_label' => 'Prüfsumme (SHA-256) – nach dem Download mit shasum -a 256 prüfen:',
        ],
        'download' => [
            'link_expired' => 'Der Download-Link ist abgelaufen. Bitte exportiere den DATEV-Bericht erneut.',
            'not_found' => 'Die Export-Datei wurde nicht gefunden. Möglicherweise wurde sie bereits gelöscht.',
        ],
    ],
];
