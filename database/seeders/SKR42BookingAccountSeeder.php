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
 *
 * Wesentliche Unterschiede zum SKR49:
 *  - Kontonummern sind 5-stellig (orientiert an SKR04)
 *  - Sphärenzuordnung erfolgt NICHT mehr über Kontonummernbereiche,
 *    sondern über das KOST1-Feld (= BookingAccountArea):
 *      IDEAL             = Ideeller Bereich
 *      ASSET_MANAGEMENT  = Vermögensverwaltung
 *      PURPOSE_OPERATION = Zweckbetrieb
 *      ECONOMIC_BUSINESS = Wirtschaftlicher Geschäftsbetrieb
 *
 * Klassenstruktur (SKR04-orientiert):
 *  0 = Anlagevermögen
 *  1 = Umlaufvermögen & Liquidität
 *  2 = Eigenkapital & Rücklagen
 *  3 = Rückstellungen & Verbindlichkeiten
 *  4 = Betriebliche Aufwendungen (sphärenübergreifend via KOST1)
 *  5 = Ideeller Bereich: Einnahmen
 *  6 = Vermögensverwaltung: Einnahmen & Aufwendungen
 *  7 = Zweckbetriebe: Einnahmen & Aufwendungen
 *  8 = Wirtschaftlicher Geschäftsbetrieb: Einnahmen & Aufwendungen
 *
 * @see https://www.datev.de/web/de/datev-shop/material/12902-datev-kontenrahmen-skr-42-vereine-stiftungen-ggmbh-4-abs-3-estg/
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
            ['number' => '00100', 'label' => 'Konzessionen, Schutzrechte und ähnliche Rechte',         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '00200', 'label' => 'Lizenzen an gewerblichen Schutzrechten',                  'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '00300', 'label' => 'EDV-Software',                                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '00500', 'label' => 'Geschäfts- oder Firmenwert',                              'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '00900', 'label' => 'Anzahlungen auf immaterielle Vermögensgegenstände',       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Grundstücke & Gebäude
            ['number' => '02000', 'label' => 'Grundstücke unbebaut',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02100', 'label' => 'Grundstücke mit Gebäuden',                                'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02200', 'label' => 'Grundstücke mit Sportanlagen',                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02300', 'label' => 'Grundstücksgleiche Rechte',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02400', 'label' => 'Gebäude',                                                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02410', 'label' => 'Vereinsheim',                                             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02420', 'label' => 'Sporthallen',                                             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02430', 'label' => 'Sportanlagen',                                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02440', 'label' => 'Vereinsgaststätte',                                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02500', 'label' => 'Außenanlagen',                                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02600', 'label' => 'Bauten auf fremden Grundstücken',                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '02900', 'label' => 'Anlagen im Bau',                                          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Technische Anlagen & Ausstattung
            ['number' => '04000', 'label' => 'Technische Anlagen und Maschinen',                        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04100', 'label' => 'Sportvorrichtungen',                                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04200', 'label' => 'Betriebs- und Geschäftsausstattung',                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04210', 'label' => 'Vereinsheimausstattung',                                  'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04220', 'label' => 'Büroeinrichtung',                                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04230', 'label' => 'Sportgeräte',                                             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04240', 'label' => 'Vereinskleidung',                                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04300', 'label' => 'Kraftfahrzeuge',                                          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04400', 'label' => 'Geringwertige Wirtschaftsgüter (bis 800 €)',               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '04410', 'label' => 'Wirtschaftsgüter Sammelposten 150–1.000 €',               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Finanzanlagen
            ['number' => '06000', 'label' => 'Anteile an verbundenen Unternehmen',                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '06100', 'label' => 'Beteiligungen',                                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '06200', 'label' => 'Wertpapiere des Anlagevermögens',                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '06300', 'label' => 'Sonstige Ausleihungen',                                   'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '06400', 'label' => 'Geleistete Kautionen',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '06500', 'label' => 'Darlehen',                                                'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 1 – Umlaufvermögen & Liquidität
            // ================================================================

            // Vorräte
            ['number' => '10000', 'label' => 'Roh-, Hilfs- und Betriebsstoffe',                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '10100', 'label' => 'Warenbestände',                                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '10200', 'label' => 'Bestände aus Sachspenden',                                'category' => $asset,   'subtype' => null,  'area' => $ideal],

            // Forderungen
            ['number' => '12000', 'label' => 'Forderungen aus Lieferungen und Leistungen',              'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],
            ['number' => '12100', 'label' => 'Forderungen aus Vereinsbereichen',                        'category' => $asset,   'subtype' => $rec,  'area' => $ideal],
            ['number' => '12200', 'label' => 'Wertberichtigungen auf Forderungen',                      'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],
            ['number' => '12900', 'label' => 'Sonstige Forderungen',                                    'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],

            // Sonstige Vermögensgegenstände
            ['number' => '13000', 'label' => 'Sonstige Vermögensgegenstände',                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '13100', 'label' => 'Geldtransit',                                             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Vorsteuer
            ['number' => '14000', 'label' => 'Abziehbare Vorsteuer',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '14100', 'label' => 'Abziehbare Vorsteuer 7 %',                                'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '14200', 'label' => 'Abziehbare Vorsteuer 19 %',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Kassen
            ['number' => '16000', 'label' => 'Kasse',                                                   'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '16010', 'label' => 'Hauptkasse',                                              'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '16020', 'label' => 'Nebenkasse 1',                                            'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '16030', 'label' => 'Nebenkasse 2',                                            'category' => $asset,   'subtype' => $cash, 'area' => $ideal],

            // Bankkonten
            ['number' => '16100', 'label' => 'Bank',                                                    'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '16110', 'label' => 'Bank 1',                                                  'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '16120', 'label' => 'Bank 2 (PayPal)',                                         'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '16130', 'label' => 'Postbank',                                                'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '16200', 'label' => 'Schecks',                                                 'category' => $asset,   'subtype' => null,  'area' => $ideal],

            // Durchlaufende Posten (bewusst von Kassen-/Bankbereich getrennt)
            ['number' => '16500', 'label' => 'Durchlaufende Posten Einnahmen',                          'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '16600', 'label' => 'Durchlaufende Posten Ausgaben',                           'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Rechnungsabgrenzung Aktiva
            ['number' => '19000', 'label' => 'Aktive Rechnungsabgrenzungsposten',                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 2 – Eigenkapital & Rücklagen
            // ================================================================
            ['number' => '20000', 'label' => 'Vereinskapital / Grundstockvermögen',                     'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '20100', 'label' => 'Kapitalerhaltungsrücklage',                               'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '20200', 'label' => 'Gezeichnetes Kapital',                                    'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '20300', 'label' => 'Kapitalrücklage',                                         'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '21000', 'label' => 'Gebundene Rücklagen § 58 Nr. 6 AO',                       'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '21100', 'label' => 'Rücklagen ideeller Bereich',                              'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '21200', 'label' => 'Rücklagen Vermögensverwaltung',                           'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '21300', 'label' => 'Rücklagen Zweckbetriebe',                                 'category' => $liab,    'subtype' => null,  'area' => $purpose],
            ['number' => '21400', 'label' => 'Rücklagen wirtschaftlicher Geschäftsbetrieb',             'category' => $liab,    'subtype' => null,  'area' => $economic],
            ['number' => '21500', 'label' => 'Freie Rücklagen § 58 Nr. 7b AO',                          'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '21600', 'label' => 'Rücklage aus Vermögensverwaltung',                        'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '21700', 'label' => 'Satzungsmäßige Rücklage',                                 'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '21800', 'label' => 'Vereinskapital § 58 Nr. 11 AO',                           'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '21900', 'label' => 'Gebundene Mittel für Förderzwecke',                       'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '22000', 'label' => 'Mittelvortrag',                                           'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '22100', 'label' => 'Jahresergebnis (Vortrag)',                                 'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '22200', 'label' => 'Ergebnisvortrag ideeller Bereich',                        'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '22300', 'label' => 'Ergebnisvortrag Vermögensverwaltung',                     'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '22400', 'label' => 'Ergebnisvortrag Zweckbetriebe',                           'category' => $liab,    'subtype' => null,  'area' => $purpose],
            ['number' => '22500', 'label' => 'Ergebnisvortrag wirtschaftlicher Geschäftsbetrieb',       'category' => $liab,    'subtype' => null,  'area' => $economic],
            ['number' => '22900', 'label' => 'Sonderposten für nicht aufwandswirksam verwendete Spenden', 'category' => $liab,  'subtype' => null,  'area' => $ideal],

            // ================================================================
            // KLASSE 3 – Rückstellungen & Verbindlichkeiten
            // ================================================================

            // Rückstellungen
            ['number' => '30000', 'label' => 'Pensionsrückstellungen',                                  'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '30100', 'label' => 'Steuerrückstellungen',                                    'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '30200', 'label' => 'Sonstige Rückstellungen',                                 'category' => $liab,    'subtype' => null,  'area' => $assetMgr],

            // Verbindlichkeiten
            ['number' => '33000', 'label' => 'Verbindlichkeiten gegenüber Kreditinstituten',            'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '33100', 'label' => 'Erhaltene Anzahlungen auf Bestellungen',                  'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '33200', 'label' => 'Verbindlichkeiten aus Lieferungen und Leistungen',        'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '33300', 'label' => 'Verbindlichkeiten für satzungsgemäße Leistungen',         'category' => $liab,    'subtype' => $pay,  'area' => $ideal],
            ['number' => '33400', 'label' => 'Verbindlichkeiten aus erteilten Zusagen',                 'category' => $liab,    'subtype' => $pay,  'area' => $ideal],
            ['number' => '33500', 'label' => 'Verbindlichkeiten aus nicht zweckentsprechend verwendeten Mitteln', 'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '33600', 'label' => 'Verbindlichkeiten aus bedingt rückzahlungspflichtigen Spenden', 'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '33700', 'label' => 'Verbindlichkeiten gegenüber Mitgliedern',                 'category' => $liab,    'subtype' => $pay,  'area' => $ideal],
            ['number' => '33800', 'label' => 'Sonstige Verbindlichkeiten',                              'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '33900', 'label' => 'Erhaltene Kautionen',                                     'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '34000', 'label' => 'Verbindlichkeiten aus Steuern und Abgaben',               'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '34100', 'label' => 'Lohnverbindlichkeiten',                                   'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],

            // Umsatzsteuer (Passiva)
            ['number' => '37000', 'label' => 'Umsatzsteuer',                                            'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '37100', 'label' => 'Umsatzsteuer 7 %',                                        'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '37200', 'label' => 'Umsatzsteuer 19 %',                                       'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '37300', 'label' => 'Umsatzsteuer laufendes Jahr',                             'category' => $liab,    'subtype' => null,  'area' => $assetMgr],

            // Rechnungsabgrenzung Passiva
            ['number' => '39000', 'label' => 'Passive Rechnungsabgrenzungsposten',                      'category' => $liab,    'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 4 – Betriebliche Aufwendungen (sphärenübergreifend via KOST1)
            // ================================================================

            // Personalkosten
            ['number' => '40000', 'label' => 'Löhne',                                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40100', 'label' => 'Gehälter',                                                'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40200', 'label' => 'Abgeführte Lohnsteuer',                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40300', 'label' => 'Sozialversicherungsbeiträge',                             'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40400', 'label' => 'Aushilfslöhne',                                          'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40500', 'label' => 'Aufwandsentschädigungen Übungsleiter (§ 3 Nr. 26 EStG)',  'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40600', 'label' => 'Ehrenamtspauschalen (§ 3 Nr. 26a EStG)',                  'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '40700', 'label' => 'Reisekostenerstattungen',                                 'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Raumkosten
            ['number' => '41000', 'label' => 'Miete und Pacht',                                         'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '41100', 'label' => 'Raumnebenkosten',                                         'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '41200', 'label' => 'Reparaturen und Instandhaltung',                          'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Verwaltungskosten
            ['number' => '42000', 'label' => 'Büromaterial',                                            'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42100', 'label' => 'Porto und Telefon',                                       'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42200', 'label' => 'Einzugskosten / Bankgebühren',                            'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42300', 'label' => 'Steuerberatungskosten',                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42400', 'label' => 'Versicherungsbeiträge',                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42500', 'label' => 'Abgaben Landesverband',                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '42600', 'label' => 'Abgaben Fachverband',                                     'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Vereinskosten
            ['number' => '43000', 'label' => 'Vereinsmitteilungen / Öffentlichkeitsarbeit',             'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '43100', 'label' => 'Geschenke, Jubiläen, Ehrungen',                          'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '43200', 'label' => 'Ausbildungskosten',                                       'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '43300', 'label' => 'Lehr- und Jugendarbeit',                                  'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '43400', 'label' => 'Repräsentationskosten',                                   'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '43500', 'label' => 'Kosten der Mitgliederverwaltung',                         'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Abschreibungen
            ['number' => '47000', 'label' => 'Abschreibungen auf Sachanlagen',                          'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '47100', 'label' => 'Abschreibungen auf GWG',                                  'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Zinsen & Finanzierungskosten
            ['number' => '48000', 'label' => 'Zinsen und ähnliche Aufwendungen',                        'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '48100', 'label' => 'Nebenkosten des Geldverkehrs',                            'category' => $expense, 'subtype' => null,  'area' => $assetMgr],

            // Sonstige betriebliche Aufwendungen
            ['number' => '49000', 'label' => 'Sonstige betriebliche Aufwendungen',                      'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // ================================================================
            // KLASSE 5 – Ideeller Bereich: Einnahmen
            // ================================================================

            // Mitgliedsbeiträge & Aufnahmegebühren
            ['number' => '50000', 'label' => 'Echte Mitgliedsbeiträge bis 300 Euro',                    'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '50100', 'label' => 'Echte Mitgliedsbeiträge 300–1.023 Euro',                  'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '50200', 'label' => 'Aufnahmegebühren',                                        'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '50300', 'label' => 'Umlagen',                                                 'category' => $income,  'subtype' => null,  'area' => $ideal],

            // Spenden
            ['number' => '51000', 'label' => 'Geldzuwendungen gegen Zuwendungsbestätigungen',           'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '51100', 'label' => 'Geldzuwendungen ohne Zuwendungsbestätigungen',            'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '51200', 'label' => 'Sachzuwendungen gegen Zuwendungsbestätigungen',           'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '51300', 'label' => 'Aufwandsspenden',                                         'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '51400', 'label' => 'Projektbezogene Spenden',                                 'category' => $income,  'subtype' => null,  'area' => $ideal],

            // Zuschüsse & Förderungen
            ['number' => '52000', 'label' => 'Zuschüsse von Verbänden',                                 'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '52100', 'label' => 'Zuschüsse von Behörden',                                  'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '52200', 'label' => 'Sonstige öffentliche Zuschüsse',                          'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '52300', 'label' => 'Zuwendungen Dritter (Sponsoren)',                         'category' => $income,  'subtype' => null,  'area' => $ideal],

            // Erbschaften & steuerneutrale Einnahmen
            ['number' => '53000', 'label' => 'Schenkungen',                                             'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '53100', 'label' => 'Erbschaften und Vermächtnisse',                           'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '54000', 'label' => 'Steuerfreie Einnahmen gemeinnütziger Vereine',            'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '59000', 'label' => 'Sonstige Einnahmen ideeller Bereich',                     'category' => $income,  'subtype' => null,  'area' => $ideal],

            // ================================================================
            // KLASSE 6 – Vermögensverwaltung: Einnahmen & Aufwendungen
            // ================================================================

            // Einnahmen
            ['number' => '60000', 'label' => 'Mieteinnahmen Gebäude',                                   'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60100', 'label' => 'Mieteinnahmen Räume',                                     'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60200', 'label' => 'Mieteinnahmen Plätze und Anlagen',                        'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60300', 'label' => 'Pachteinnahmen',                                          'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60400', 'label' => 'Zinserträge aus Bankguthaben',                            'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60500', 'label' => 'Dividenden und Gewinnanteile',                            'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60600', 'label' => 'Veräußerungserlöse Anlagevermögen',                       'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '60700', 'label' => 'Steuerneutrale Einnahmen Vermögensverwaltung',            'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '69000', 'label' => 'Sonstige Einnahmen Vermögensverwaltung',                  'category' => $income,  'subtype' => null,  'area' => $assetMgr],

            // Aufwendungen
            ['number' => '65000', 'label' => 'Instandhaltung/Reparatur Vermietungsobjekte',             'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '65100', 'label' => 'Grundsteuer',                                             'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '65200', 'label' => 'Versicherungen Vermögensverwaltung',                      'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '65900', 'label' => 'Sonstige Kosten Vermögensverwaltung',                     'category' => $expense, 'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 7 – Zweckbetriebe: Einnahmen & Aufwendungen
            // ================================================================

            // Einnahmen Zweckbetrieb Sport
            ['number' => '70000', 'label' => 'Eintrittsgelder aus sportlichen Veranstaltungen',         'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '70100', 'label' => 'Startgelder',                                             'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '70200', 'label' => 'Kurs- und Lehrgangsgebühren Sport',                       'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '70300', 'label' => 'Einnahmen aus Sportveranstaltungen',                      'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '70400', 'label' => 'Transferentschädigungen für Sportler',                    'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '79000', 'label' => 'Sonstige Einnahmen Zweckbetrieb Sport',                   'category' => $income,  'subtype' => null,  'area' => $purpose],

            // Aufwendungen Zweckbetrieb Sport
            ['number' => '75000', 'label' => 'Sportbedarf',                                             'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '75100', 'label' => 'Kosten sportlicher Veranstaltungen',                      'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '75200', 'label' => 'Reisekosten Sport',                                       'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '75300', 'label' => 'Schiedsrichtergebühren',                                  'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '75400', 'label' => 'Meldegebühren und Mannschaftskosten',                     'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '75900', 'label' => 'Sonstige Kosten Zweckbetrieb Sport',                      'category' => $expense, 'subtype' => null,  'area' => $purpose],

            // Einnahmen sonstige Zweckbetriebe (Kultur, Bildung, Wohlfahrt)
            ['number' => '71000', 'label' => 'Eintrittsgelder kulturelle Veranstaltungen',              'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '71100', 'label' => 'Einnahmen aus Wohlfahrtspflege',                          'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '71200', 'label' => 'Kursgebühren sonstige Zweckbetriebe',                     'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '71300', 'label' => 'Einnahmen aus Bildungsmaßnahmen',                         'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '79100', 'label' => 'Sonstige Einnahmen sonstige Zweckbetriebe',               'category' => $income,  'subtype' => null,  'area' => $purpose],

            // Aufwendungen sonstige Zweckbetriebe
            ['number' => '76000', 'label' => 'Material-/Sachkosten sonstige Zweckbetriebe',             'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '76100', 'label' => 'Veranstaltungskosten',                                    'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '76900', 'label' => 'Sonstige Kosten sonstige Zweckbetriebe',                  'category' => $expense, 'subtype' => null,  'area' => $purpose],

            // ================================================================
            // KLASSE 8 – Wirtschaftlicher Geschäftsbetrieb: Einnahmen & Aufwendungen
            // ================================================================

            // Einnahmen
            ['number' => '80000', 'label' => 'Einnahmen aus Vereinsgaststätte/Bewirtung',               'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80100', 'label' => 'Einnahmen aus Altmaterialsammlung',                       'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80200', 'label' => 'Einnahmen aus Vermietung an Dritte (steuerpflichtig)',     'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80300', 'label' => 'Einnahmen aus Werbung',                                   'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80310', 'label' => 'Trikotwerbung',                                           'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80320', 'label' => 'Bandenwerbung',                                           'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80400', 'label' => 'Einnahmen aus Warenverkauf',                              'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '80500', 'label' => 'Eintrittsgelder Sport über 35.000 € Umsatz',              'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '89000', 'label' => 'Sonstige Einnahmen wirtschaftlicher Geschäftsbetrieb',    'category' => $income,  'subtype' => null,  'area' => $economic],

            // Aufwendungen
            ['number' => '85000', 'label' => 'Wareneinkauf Gaststätte/Bewirtung',                       'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '85100', 'label' => 'Wareneinkauf sonstige wirtschaftliche Betriebe',          'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '85200', 'label' => 'Werbekosten wirtschaftlicher Geschäftsbetrieb',           'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '85300', 'label' => 'Körperschaftsteuer',                                      'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '85400', 'label' => 'Gewerbesteuer',                                           'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '89900', 'label' => 'Sonstige Kosten wirtschaftlicher Geschäftsbetrieb',       'category' => $expense, 'subtype' => null,  'area' => $economic],
        ];
    }
}
