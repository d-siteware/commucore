<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use App\Models\Accounting\BookingAccount;
use Illuminate\Database\Seeder;

/**
 * SKR42 – Standardkontenrahmen für Vereine, Stiftungen und gemeinnützige Organisationen
 *
 * Ablösung des SKR49 ab Wirtschaftsjahr 2025.
 * Kontonummern gemäß DATEV Art.-Nr. 12901 (gültig ab 2025-01-01).
 *
 * Wesentliche Unterschiede zum SKR49:
 *  - Kontonummern sind 5-stellig (orientiert an SKR04)
 *  - Sphärenzuordnung erfolgt NICHT mehr über Kontonummernbereiche,
 *    sondern über das KOST1-Feld (= BookingAccountArea):
 *      IDEAL             = 1 – Ideeller Bereich
 *      ASSET_MANAGEMENT  = 2 – Vermögensverwaltung
 *      PURPOSE_OPERATION = 3 – Zweckbetrieb
 *      ECONOMIC_BUSINESS = 4 – Wirtschaftlicher Geschäftsbetrieb
 *
 * Klassenstruktur (SKR04-orientiert, DATEV Art.-Nr. 12901):
 *  0 = Anlagevermögen
 *  1 = Umlaufvermögen & Liquidität (Kasse 1600x, Bank 1800x)
 *  2 = Eigenkapital & Rücklagen (vereinsspezifisch: 2000–2997)
 *  3 = Rückstellungen & Verbindlichkeiten
 *  4 = Erträge ideeller Bereich (Mitgliedsbeiträge, Spenden, Zuschüsse)
 *  5 = Umsatzerlöse / sonstige Erträge (Vermögensverwaltung, Zweckbetrieb, wGB)
 *  6 = Personalaufwendungen & betriebliche Aufwendungen
 *  7 = Weitere betriebliche Aufwendungen (Raum, Verwaltung, Sonstiges)
 *  8 = Abschreibungen, Zinsen, Steuern
 *
 * @see https://www.datev.de/web/de/datev-shop/material/skr-42/ (Art.-Nr. 12901)
 */
class SKR42BookingAccountSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->accounts() as $account) {
            BookingAccount::updateOrCreate(
                ['number' => $account['number']],
                [
                    'label' => $account['label'],
                    'category' => $account['category'],
                    'subtype' => $account['subtype'],
                    'area' => $account['area'],
                ]
            );
        }
    }

    /**
     * @return array<int, array{
     *   number: string,
     *   label: string,
     *   category: string,
     *   subtype: string|null,
     *   area: string
     * }>
     */
    private function accounts(): array
    {
        $ideal = BookingAccountArea::IDEAL->value;
        $assetMgr = BookingAccountArea::ASSET_MANAGEMENT->value;
        $purpose = BookingAccountArea::PURPOSE_OPERATION->value;
        $economic = BookingAccountArea::ECONOMIC_BUSINESS->value;

        $asset = AccountCategory::Asset->value;
        $liab = AccountCategory::Liability->value;
        $income = AccountCategory::Income->value;
        $expense = AccountCategory::Expense->value;

        $bank = AccountSubtype::Bank->value;
        $cash = AccountSubtype::Cash->value;
        $rec = AccountSubtype::Receivable->value;
        $pay = AccountSubtype::Payable->value;

        return [
            // ================================================================
            // KLASSE 0 – Anlagevermögen
            // ================================================================

            // Immaterielle Vermögensgegenstände
            ['number' => '01000', 'label' => 'Entgeltlich erworbene Konzessionen, Schutzrechte und ähnliche Rechte', 'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '01100', 'label' => 'Konzessionen',                                                          'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '01200', 'label' => 'Gewerbliche Schutzrechte',                                              'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '01350', 'label' => 'EDV-Software',                                                          'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '01500', 'label' => 'Geschäfts- oder Firmenwert',                                            'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '01700', 'label' => 'Geleistete Anzahlungen auf immaterielle Vermögensgegenstände',          'category' => $asset, 'subtype' => null, 'area' => $assetMgr],

            // Sachanlagen – Grundstücke & Gebäude
            ['number' => '02000', 'label' => 'Grundstücke, grundstücksgleiche Rechte und Bauten',                    'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02150', 'label' => 'Unbebaute Grundstücke',                                                 'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02300', 'label' => 'Bauten auf eigenen Grundstücken',                                       'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02410', 'label' => 'Gebäude',                                                               'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02430', 'label' => 'Hallen',                                                                'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02440', 'label' => 'Gaststätte',                                                            'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '02800', 'label' => 'Außenanlagen',                                                          'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '03300', 'label' => 'Bauten auf fremden Grundstücken',                                       'category' => $asset, 'subtype' => null, 'area' => $assetMgr],

            // Sachanlagen – Technische Anlagen & Ausstattung
            ['number' => '04000', 'label' => 'Technische Anlagen und Maschinen',                                      'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '05000', 'label' => 'Andere Anlagen, Betriebs- und Geschäftsausstattung',                   'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '05200', 'label' => 'Pkw',                                                                   'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '05400', 'label' => 'Lkw',                                                                   'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '06300', 'label' => 'Betriebsausstattung',                                                   'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '06500', 'label' => 'Büroeinrichtung',                                                       'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '06700', 'label' => 'Geringwertige Wirtschaftsgüter',                                        'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '06750', 'label' => 'Wirtschaftsgüter (Sammelposten)',                                       'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '07000', 'label' => 'Geleistete Anzahlungen und Anlagen im Bau',                            'category' => $asset, 'subtype' => null, 'area' => $assetMgr],

            // Finanzanlagen
            ['number' => '08000', 'label' => 'Anteile an verbundenen Unternehmen (Anlagevermögen)',                  'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '08200', 'label' => 'Beteiligungen',                                                         'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '09000', 'label' => 'Wertpapiere des Anlagevermögens',                                      'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '09300', 'label' => 'Übrige sonstige Ausleihungen',                                          'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '09350', 'label' => 'Sonstige Ausleihungen – geleistete Kautionen',                         'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '09400', 'label' => 'Darlehen',                                                              'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '09800', 'label' => 'Genossenschaftsanteile zum langfristigen Verbleib',                    'category' => $asset, 'subtype' => null, 'area' => $assetMgr],

            // ================================================================
            // KLASSE 1 – Umlaufvermögen & Liquidität
            // ================================================================

            // Vorräte
            ['number' => '10000', 'label' => 'Roh-, Hilfs- und Betriebsstoffe (Bestand)',                            'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '11000', 'label' => 'Fertige Erzeugnisse und Waren (Bestand)',                              'category' => $asset, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '11600', 'label' => 'Waren und Material aus Sachspenden (Bestand)',                         'category' => $asset, 'subtype' => null, 'area' => $ideal],

            // Forderungen
            ['number' => '12000', 'label' => 'Forderungen aus Lieferungen und Leistungen',                           'category' => $asset, 'subtype' => $rec,  'area' => $assetMgr],
            ['number' => '13000', 'label' => 'Sonstige Vermögensgegenstände',                                        'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '13500', 'label' => 'Kautionen',                                                            'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '13700', 'label' => 'Durchlaufende Posten',                                                  'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '13720', 'label' => 'Geldtransit',                                                          'category' => $asset, 'subtype' => null,  'area' => $assetMgr],

            // Vorsteuer
            ['number' => '14000', 'label' => 'Abziehbare Vorsteuer',                                                 'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '14010', 'label' => 'Abziehbare Vorsteuer 7 %',                                             'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '14060', 'label' => 'Abziehbare Vorsteuer 19 %',                                            'category' => $asset, 'subtype' => null,  'area' => $assetMgr],

            // Kassen – DATEV: 1600x
            ['number' => '16000', 'label' => 'Kasse',                                                                'category' => $asset, 'subtype' => $cash, 'area' => $ideal],
            ['number' => '16100', 'label' => 'Nebenkasse 1',                                                         'category' => $asset, 'subtype' => $cash, 'area' => $ideal],
            ['number' => '16200', 'label' => 'Nebenkasse 2',                                                         'category' => $asset, 'subtype' => $cash, 'area' => $ideal],

            // Bank (Postbank) – DATEV: 1700x
            ['number' => '17000', 'label' => 'Bank (Postbank)',                                                      'category' => $asset, 'subtype' => $bank, 'area' => $ideal],

            // Bank – DATEV: 1800x
            ['number' => '18000', 'label' => 'Bank',                                                                 'category' => $asset, 'subtype' => $bank, 'area' => $ideal],
            ['number' => '18100', 'label' => 'Bank 1',                                                               'category' => $asset, 'subtype' => $bank, 'area' => $ideal],
            ['number' => '18200', 'label' => 'Bank 2',                                                               'category' => $asset, 'subtype' => $bank, 'area' => $ideal],
            ['number' => '18300', 'label' => 'Bank 3',                                                               'category' => $asset, 'subtype' => $bank, 'area' => $ideal],
            ['number' => '18400', 'label' => 'Bank 4',                                                               'category' => $asset, 'subtype' => $bank, 'area' => $ideal],
            ['number' => '18500', 'label' => 'Bank 5',                                                               'category' => $asset, 'subtype' => $bank, 'area' => $ideal],

            // Schecks
            ['number' => '15500', 'label' => 'Schecks',                                                              'category' => $asset, 'subtype' => null,  'area' => $ideal],

            // Rechnungsabgrenzung Aktiva
            ['number' => '19000', 'label' => 'Aktive Rechnungsabgrenzung',                                           'category' => $asset, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '19400', 'label' => 'Damnum / Disagio',                                                     'category' => $asset, 'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 2 – Eigenkapital & Rücklagen (vereinsspezifisch)
            // ================================================================

            // Rücklagen (Vereine/Stiftungen)
            ['number' => '20000', 'label' => 'Gebundene Rücklagen nach § 62 Abs. 1 Nr. 1 AO',                       'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '20800', 'label' => 'Wiederbeschaffungsrücklage',                                           'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '20900', 'label' => 'Betriebsmittelrücklage',                                               'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '21000', 'label' => 'Freie Rücklagen nach § 62 Abs. 1 Nr. 3 AO',                           'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '22000', 'label' => 'Rücklage aus sonstigen zeitnah zu verwendenden Mitteln',               'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '22500', 'label' => 'Rücklagen zum Erwerb von Gesellschaftsrechten nach § 62 Abs. 1 Nr. 4 AO', 'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '23000', 'label' => 'Nutzungsgebundenes Kapital (Eigenkapitalausweis)',                      'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '24000', 'label' => 'Ergebnisse Vermögensumschichtung',                                     'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '25000', 'label' => 'Vereinskapital / sonstige nicht zeitnah zu verwendende Mittel nach § 62 Abs. 3 AO', 'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '26000', 'label' => 'Errichtungskapital',                                                   'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '26500', 'label' => 'Zustiftungskapital',                                                   'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '27000', 'label' => 'Zuführung aus Ergebnisrücklagen',                                      'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '27500', 'label' => 'Kapitalerhaltungsrücklage',                                            'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '28000', 'label' => 'Ansparrücklage nach § 62 Abs. 4 AO',                                  'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '28500', 'label' => 'Sonstige Ergebnisrücklagen',                                           'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '29700', 'label' => 'Gewinnvortrag / Ergebnisvortrag vor Verwendung',                       'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '29780', 'label' => 'Verlustvortrag / Ergebnisvortrag vor Verwendung',                      'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '29960', 'label' => 'Längerfristig gebundene Spenden',                                      'category' => $liab, 'subtype' => null, 'area' => $ideal],
            ['number' => '29970', 'label' => 'Noch nicht satzungsgemäß verwendete Spenden',                          'category' => $liab, 'subtype' => null, 'area' => $ideal],

            // ================================================================
            // KLASSE 3 – Rückstellungen & Verbindlichkeiten
            // ================================================================

            // Rückstellungen
            ['number' => '30000', 'label' => 'Rückstellungen für Pensionen und ähnliche Verpflichtungen',            'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '30200', 'label' => 'Steuerrückstellungen',                                                  'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '30700', 'label' => 'Sonstige Rückstellungen',                                               'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '30790', 'label' => 'Urlaubsrückstellungen',                                                 'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '30950', 'label' => 'Rückstellungen für Abschluss- und Prüfungskosten',                     'category' => $liab, 'subtype' => null, 'area' => $assetMgr],

            // Verbindlichkeiten gegenüber Kreditinstituten
            ['number' => '31500', 'label' => 'Verbindlichkeiten gegenüber Kreditinstituten',                         'category' => $liab, 'subtype' => $pay, 'area' => $assetMgr],

            // Verbindlichkeiten aus Lieferungen und Leistungen
            ['number' => '33000', 'label' => 'Verbindlichkeiten aus Lieferungen und Leistungen',                     'category' => $liab, 'subtype' => $pay, 'area' => $assetMgr],

            // Sonstige Verbindlichkeiten
            ['number' => '34800', 'label' => 'Verbindlichkeiten aus Steuern und Abgaben',                            'category' => $liab, 'subtype' => $pay, 'area' => $assetMgr],
            ['number' => '34900', 'label' => 'Verbindlichkeiten im Rahmen der sozialen Sicherheit',                  'category' => $liab, 'subtype' => $pay, 'area' => $assetMgr],
            ['number' => '35000', 'label' => 'Sonstige Verbindlichkeiten',                                           'category' => $liab, 'subtype' => $pay, 'area' => $assetMgr],
            ['number' => '35100', 'label' => 'Verbindlichkeiten gegenüber Mitgliedern',                              'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '35200', 'label' => 'Verbindlichkeiten aus nicht zweckentsprechend verwendeten Mitteln',    'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '35300', 'label' => 'Verbindlichkeiten aus bedingt rückzahlungspflichtigen Spenden',        'category' => $liab, 'subtype' => $pay, 'area' => $ideal],

            // Umsatzsteuer (Passiva)
            ['number' => '37500', 'label' => 'Umsatzsteuer',                                                         'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '37510', 'label' => 'Umsatzsteuer 7 %',                                                     'category' => $liab, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '37520', 'label' => 'Umsatzsteuer 19 %',                                                    'category' => $liab, 'subtype' => null, 'area' => $assetMgr],

            // Rechnungsabgrenzung Passiva
            ['number' => '39000', 'label' => 'Passive Rechnungsabgrenzungsposten',                                   'category' => $liab, 'subtype' => null, 'area' => $assetMgr],

            // ================================================================
            // KLASSE 4 – Erträge ideeller Bereich
            // (DATEV Art.-Nr. 12901: Konto 4000 ff.)
            // ================================================================

            // Mitgliedsbeiträge & Aufnahmegebühren
            ['number' => '40000', 'label' => 'Echte Mitgliedsbeiträge',                                              'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40100', 'label' => 'Aufnahmegebühren',                                                     'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40200', 'label' => 'Einnahmen aus Mitgliederumlagen',                                      'category' => $income, 'subtype' => null, 'area' => $ideal],

            // Erbschaften & steuerneutrale Einnahmen
            ['number' => '40300', 'label' => 'Einnahmen aus Schenkungen',                                            'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40310', 'label' => 'Einnahmen aus Erbschaften',                                            'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40320', 'label' => 'Einnahmen aus Vermächtnissen',                                         'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40330', 'label' => 'Übrige ertragsteuerneutrale Einnahmen',                               'category' => $income, 'subtype' => null, 'area' => $ideal],

            // Spenden / Zuwendungen
            ['number' => '40400', 'label' => 'Erträge aus Spenden / Zuwendungen',                                    'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40450', 'label' => 'Erträge aus Sachzuwendungen',                                          'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40460', 'label' => 'Aufwandsspenden',                                                      'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40470', 'label' => 'Projektbezogene Spenden',                                              'category' => $income, 'subtype' => null, 'area' => $ideal],

            // Zuschüsse & Förderungen
            ['number' => '40500', 'label' => 'Zuschüsse von Verbänden',                                              'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40510', 'label' => 'Zuschüsse von Behörden / öffentliche Förderung',                      'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40520', 'label' => 'Zuwendungen Dritter (Sponsoren)',                                      'category' => $income, 'subtype' => null, 'area' => $ideal],

            // Steuerfreie Einnahmen
            ['number' => '40600', 'label' => 'Steuerfreie Einnahmen gemeinnütziger Vereine',                         'category' => $income, 'subtype' => null, 'area' => $ideal],
            ['number' => '40900', 'label' => 'Sonstige Einnahmen ideeller Bereich',                                  'category' => $income, 'subtype' => null, 'area' => $ideal],

            // ================================================================
            // KLASSE 5 – Umsatzerlöse (Vermögensverwaltung, Zweckbetrieb, wGB)
            // ================================================================

            // Vermögensverwaltung – Erträge
            ['number' => '50000', 'label' => 'Mieteinnahmen Gebäude',                                                'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50100', 'label' => 'Mieteinnahmen Räume',                                                  'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50200', 'label' => 'Mieteinnahmen Plätze und Anlagen',                                     'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50300', 'label' => 'Pachteinnahmen',                                                       'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50400', 'label' => 'Zinserträge aus Bankguthaben',                                         'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50500', 'label' => 'Dividenden und Gewinnanteile',                                         'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50600', 'label' => 'Veräußerungserlöse Anlagevermögen',                                    'category' => $income, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '50900', 'label' => 'Sonstige Einnahmen Vermögensverwaltung',                               'category' => $income, 'subtype' => null, 'area' => $assetMgr],

            // Zweckbetrieb – Erträge Sport
            ['number' => '51000', 'label' => 'Eintrittsgelder aus sportlichen Veranstaltungen',                      'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51100', 'label' => 'Startgelder',                                                          'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51200', 'label' => 'Kurs- und Lehrgangsgebühren Sport',                                    'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51300', 'label' => 'Einnahmen aus Sportveranstaltungen',                                   'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51400', 'label' => 'Transferentschädigungen für Sportler',                                 'category' => $income, 'subtype' => null, 'area' => $purpose],

            // Zweckbetrieb – Erträge sonstige (Kultur, Bildung, Wohlfahrt)
            ['number' => '51500', 'label' => 'Eintrittsgelder kulturelle Veranstaltungen',                           'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51600', 'label' => 'Einnahmen aus Wohlfahrtspflege',                                       'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51700', 'label' => 'Kursgebühren sonstige Zweckbetriebe',                                  'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51800', 'label' => 'Einnahmen aus Bildungsmaßnahmen',                                      'category' => $income, 'subtype' => null, 'area' => $purpose],
            ['number' => '51900', 'label' => 'Sonstige Einnahmen Zweckbetriebe',                                     'category' => $income, 'subtype' => null, 'area' => $purpose],

            // Wirtschaftlicher Geschäftsbetrieb – Erträge
            ['number' => '52000', 'label' => 'Einnahmen aus Vereinsgaststätte / Bewirtung',                         'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52100', 'label' => 'Einnahmen aus Altmaterialsammlung',                                    'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52200', 'label' => 'Einnahmen aus Vermietung an Dritte (steuerpflichtig)',                 'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52300', 'label' => 'Einnahmen aus Werbung',                                               'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52310', 'label' => 'Trikotwerbung',                                                        'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52320', 'label' => 'Bandenwerbung',                                                        'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52400', 'label' => 'Einnahmen aus Warenverkauf',                                           'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52500', 'label' => 'Eintrittsgelder Sport (über Zweckbetriebsgrenze)',                     'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '52900', 'label' => 'Sonstige Einnahmen wirtschaftlicher Geschäftsbetrieb',                 'category' => $income, 'subtype' => null, 'area' => $economic],

            // ================================================================
            // KLASSE 6 – Personalaufwendungen & betriebliche Aufwendungen
            // (DATEV Art.-Nr. 12901: Konto 6000 ff.)
            // ================================================================

            // Vereinsspezifische Personalaufwendungen
            ['number' => '60020', 'label' => 'Ehrenamtspauschale (§ 3 Nr. 26a EStG)',                               'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '60040', 'label' => 'Übungsleiterpauschale (§ 3 Nr. 26 EStG)',                             'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Löhne & Gehälter
            ['number' => '60100', 'label' => 'Löhne',                                                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '60200', 'label' => 'Gehälter',                                                             'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '60300', 'label' => 'Aushilfslöhne',                                                        'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '60350', 'label' => 'Löhne für Minijobs',                                                   'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '60360', 'label' => 'Pauschale Steuer für Minijobber',                                      'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Sozialabgaben
            ['number' => '61000', 'label' => 'Gesetzliche soziale Aufwendungen',                                     'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '61100', 'label' => 'Arbeitgeberanteil Krankenversicherung',                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '61200', 'label' => 'Beiträge zur Berufsgenossenschaft',                                    'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '61300', 'label' => 'Arbeitgeberanteil Rentenversicherung',                                 'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '61400', 'label' => 'Arbeitgeberanteil Arbeitslosenversicherung',                           'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '61500', 'label' => 'Arbeitgeberanteil Pflegeversicherung',                                 'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Sonstige Personalaufwendungen
            ['number' => '62000', 'label' => 'Reisekostenerstattungen',                                              'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '62100', 'label' => 'Freiwillige soziale Aufwendungen',                                     'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Raumkosten
            ['number' => '63000', 'label' => 'Miete',                                                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63150', 'label' => 'Pacht',                                                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63200', 'label' => 'Raumnebenkosten',                                                      'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63300', 'label' => 'Reinigungskosten',                                                     'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Versicherungen, Beiträge, Abgaben
            ['number' => '63800', 'label' => 'Versicherungsbeiträge',                                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63900', 'label' => 'Sonstige Abgaben und Beiträge',                                        'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63910', 'label' => 'Abgaben Landesverband',                                                'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63920', 'label' => 'Abgaben Fachverband',                                                  'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Betriebliche Aufwendungen
            ['number' => '64000', 'label' => 'Büromaterial',                                                         'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64100', 'label' => 'Porto',                                                                 'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64110', 'label' => 'Telefon',                                                               'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64200', 'label' => 'Werbekosten',                                                           'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64210', 'label' => 'Vereinsmitteilungen / Öffentlichkeitsarbeit',                          'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64400', 'label' => 'Abschluss- und Prüfungskosten / Steuerberatungskosten',                'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Reparaturen & Instandhaltung
            ['number' => '64800', 'label' => 'Reparaturen und Instandhaltung',                                       'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '64900', 'label' => 'Instandhaltung/Reparatur Vermietungsobjekte',                          'category' => $expense, 'subtype' => null, 'area' => $assetMgr],

            // Vereinsspezifische Kosten (Gemeinützig)
            ['number' => '63010', 'label' => 'Verwaltungskosten',                                                    'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63020', 'label' => 'Geschenke, Jubiläen, Ehrungen',                                        'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63030', 'label' => 'Ausbildungskosten',                                                    'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63040', 'label' => 'Lehr- und Jugendarbeit',                                               'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '63050', 'label' => 'Kosten der Mitgliederverwaltung',                                      'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Wareneinkauf (wGB)
            ['number' => '65000', 'label' => 'Wareneinkauf Gaststätte / Bewirtung',                                  'category' => $expense, 'subtype' => null, 'area' => $economic],
            ['number' => '65100', 'label' => 'Wareneinkauf sonstige wirtschaftliche Betriebe',                       'category' => $expense, 'subtype' => null, 'area' => $economic],

            // Sportbetrieb (Zweckbetrieb)
            ['number' => '67000', 'label' => 'Sportbedarf',                                                          'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67100', 'label' => 'Kosten sportlicher Veranstaltungen',                                   'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67200', 'label' => 'Reisekosten Sport',                                                    'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67300', 'label' => 'Schiedsrichtergebühren',                                               'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67400', 'label' => 'Meldegebühren und Mannschaftskosten',                                  'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67500', 'label' => 'Material-/Sachkosten sonstige Zweckbetriebe',                         'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67600', 'label' => 'Veranstaltungskosten',                                                 'category' => $expense, 'subtype' => null, 'area' => $purpose],
            ['number' => '67900', 'label' => 'Sonstige Kosten Zweckbetriebe',                                        'category' => $expense, 'subtype' => null, 'area' => $purpose],

            // Sonstige betriebliche Aufwendungen
            ['number' => '69000', 'label' => 'Sonstige betriebliche Aufwendungen',                                   'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '69100', 'label' => 'Bankgebühren / Einzugskosten',                                         'category' => $expense, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '69200', 'label' => 'Grundsteuer',                                                          'category' => $expense, 'subtype' => null, 'area' => $assetMgr],

            // ================================================================
            // KLASSE 8 – Abschreibungen, Zinsen, Steuern, Sonstiges
            // ================================================================

            // Abschreibungen
            ['number' => '82000', 'label' => 'Abschreibungen auf Sachanlagen',                                       'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '82100', 'label' => 'Abschreibungen auf GWG',                                               'category' => $expense, 'subtype' => null, 'area' => $ideal],
            ['number' => '82200', 'label' => 'Abschreibungen auf immaterielle Vermögensgegenstände',                 'category' => $expense, 'subtype' => null, 'area' => $ideal],

            // Zinsen & Finanzierungskosten
            ['number' => '85100', 'label' => 'Zinsen und ähnliche Aufwendungen',                                     'category' => $expense, 'subtype' => null, 'area' => $assetMgr],
            ['number' => '86000', 'label' => 'Zinserträge',                                                          'category' => $income,  'subtype' => null, 'area' => $assetMgr],

            // Steuern vom Einkommen und Ertrag
            ['number' => '89000', 'label' => 'Körperschaftsteuer',                                                   'category' => $expense, 'subtype' => null, 'area' => $economic],
            ['number' => '89100', 'label' => 'Gewerbesteuer',                                                        'category' => $expense, 'subtype' => null, 'area' => $economic],
        ];
    }
}
