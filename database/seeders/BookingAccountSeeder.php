<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use App\Models\Accounting\BookingAccount;
use Illuminate\Database\Seeder;

/**
 * SKR49 – Standardkontenrahmen für Vereine, Stiftungen und gemeinnützige Organisationen
 *
 * @see https://www.standardkontenrahmen.de/skr49
 */
class BookingAccountSeeder extends Seeder
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
            // KLASSE 0 – Bestandskonten Aktiva (Anlagevermögen)
            // ================================================================
            ['number' => '1',    'label' => 'Ansprüche auf Einzahlung in das Stiftungskapital',  'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '10',   'label' => 'Konzessionen, Schutzrechte und ähnliche Rechte',    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '15',   'label' => 'Konzessionen',                                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '20',   'label' => 'Gewerbliche Schutzrechte',                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '27',   'label' => 'EDV-Software',                                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '30',   'label' => 'Lizenzen an gewerblichen Schutzrechten',             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '35',   'label' => 'Geschäfts- oder Firmenwert',                         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '39',   'label' => 'Anzahlungen auf immaterielle Vermögensgegenstände',  'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Grundstücke & Gebäude
            ['number' => '50',   'label' => 'Grundstücke unbebaut',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '55',   'label' => 'Grundstücke mit Gebäuden bebaut',                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '60',   'label' => 'Grundstücke mit Anlagen bebaut',                     'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '70',   'label' => 'Grundstücksgleiche Rechte',                          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '100',  'label' => 'Gebäude',                                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '110',  'label' => 'Vereinsheim',                                        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '111',  'label' => 'Sporthallen',                                        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '112',  'label' => 'Sportanlagen',                                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '120',  'label' => 'Vereinsgaststätte',                                  'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '125',  'label' => 'Sonstige Vereinsgebäude',                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '130',  'label' => 'Geschäftsbauten',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '150',  'label' => 'Garagen',                                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '155',  'label' => 'Außenanlagen',                                       'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '170',  'label' => 'Einrichtungen für Gebäude',                          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '175',  'label' => 'Ausbauten, Anbauten und Zubauten',                   'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '185',  'label' => 'Bauten auf fremden Grundstücken',                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Technische Anlagen & Ausstattung
            ['number' => '200',  'label' => 'Technische Anlagen',                                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '205',  'label' => 'Maschinen',                                          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '210',  'label' => 'Betriebsvorrichtungen',                              'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '215',  'label' => 'Sportvorrichtungen',                                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '220',  'label' => 'Vereinsheimausstattung',                             'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '250',  'label' => 'Kraftfahrzeuge, Transportmittel',                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '255',  'label' => 'PKW',                                                'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '260',  'label' => 'Anhänger',                                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '300',  'label' => 'Vereinsausstattung',                                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '305',  'label' => 'Vereinskleidung',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '310',  'label' => 'Sportgeräte',                                        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '320',  'label' => 'Büroeinrichtung',                                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '330',  'label' => 'Einrichtung Vereinsheim',                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '335',  'label' => 'Sonstiges Inventar',                                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '340',  'label' => 'Geringwertige Wirtschaftsgüter (bis 800 €)',          'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '341',  'label' => 'Wirtschaftsgüter Sammelposten 150–1000 €',           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '400',  'label' => 'Sonstige Anlagen und Ausstattung',                   'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '405',  'label' => 'Betriebsausstattung',                                'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '410',  'label' => 'Geschäftsausstattung',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '480',  'label' => 'Geleistete Anzahlungen Grundstücke/Gebäude',         'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '485',  'label' => 'Gebäude im Bau',                                     'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '490',  'label' => 'Geleistete Anzahlungen sonstige Sachanlagen',        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Finanzanlagen
            ['number' => '500',  'label' => 'Anteile an verbundenen Unternehmen',                 'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '510',  'label' => 'Beteiligungen',                                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '545',  'label' => 'Wertpapiere des Anlagevermögens',                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '550',  'label' => 'Sonstige Ausleihungen',                              'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '555',  'label' => 'Geleistete Kautionen',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '560',  'label' => 'Darlehen',                                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Umlaufvermögen
            ['number' => '600',  'label' => 'Roh-, Hilfs- und Betriebsstoffe',                    'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '620',  'label' => 'Warenbestände',                                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '625',  'label' => 'Bestände Waren/Material aus Sachspenden',            'category' => $asset,   'subtype' => null,  'area' => $ideal],
            ['number' => '650',  'label' => 'Forderungen aus Lieferungen und Leistungen',         'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],
            ['number' => '655',  'label' => 'Forderungen aus Vereinsbereichen',                   'category' => $asset,   'subtype' => $rec,  'area' => $ideal],
            ['number' => '665',  'label' => 'Wertberichtigungen Forderungen L+L',                 'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],
            ['number' => '700',  'label' => 'Sonstige Vermögensgegenstände',                      'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '705',  'label' => 'Geldtransit',                                        'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '725',  'label' => 'Sonstige Forderungen',                               'category' => $asset,   'subtype' => $rec,  'area' => $assetMgr],

            // Vorsteuer
            ['number' => '770',  'label' => 'Abziehbare Vorsteuer',                               'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '775',  'label' => 'Abziehbare Vorsteuer 7%',                            'category' => $asset,   'subtype' => null,  'area' => $assetMgr],
            ['number' => '780',  'label' => 'Abziehbare Vorsteuer 19%',                           'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // Durchlaufende Posten
            ['number' => '870',  'label' => 'Durchlaufende Posten Einnahmen',                     'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '875',  'label' => 'Durchlaufende Posten Ausgaben',                      'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // Liquiditätskonten
            ['number' => '920',  'label' => 'Kasse',                                              'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '925',  'label' => 'Hauptkasse',                                         'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '930',  'label' => 'Nebenkasse 1',                                       'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '935',  'label' => 'Nebenkasse 2',                                       'category' => $asset,   'subtype' => $cash, 'area' => $ideal],
            ['number' => '940',  'label' => 'Postbank',                                           'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '945',  'label' => 'Bank',                                               'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '950',  'label' => 'Bank 1 (PayPal)',                                    'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '955',  'label' => 'Bank 2',                                             'category' => $asset,   'subtype' => $bank, 'area' => $ideal],
            ['number' => '960',  'label' => 'Schecks',                                            'category' => $asset,   'subtype' => null,  'area' => $ideal],
            ['number' => '990',  'label' => 'Rechnungsabgrenzungsposten aktiv',                   'category' => $asset,   'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 1 – Bestandskonten Passiva (Rücklagen, Verbindlichkeiten)
            // ================================================================
            ['number' => '1000', 'label' => 'Gebundene Rücklagen § 58 Nr. 6 AO',                  'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1010', 'label' => 'Rücklagen ideeller Bereich',                         'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1020', 'label' => 'Rücklagen Vermögensverwaltung',                      'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1030', 'label' => 'Rücklagen Zweckbetriebe',                            'category' => $liab,    'subtype' => null,  'area' => $purpose],
            ['number' => '1040', 'label' => 'Rücklagen Geschäftsbetriebe',                        'category' => $liab,    'subtype' => null,  'area' => $economic],
            ['number' => '1070', 'label' => 'Freie Rücklagen',                                    'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1074', 'label' => 'Rücklage aus Vermögensverwaltung',                   'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1075', 'label' => 'Rücklage aus sonstigen zeitnah zu verwendenden Mitteln', 'category' => $liab, 'subtype' => null,  'area' => $ideal],
            ['number' => '1076', 'label' => 'Freie Rücklagen § 58 Nr. 7b AO',                     'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1080', 'label' => 'Ergebnisvorträge allgemein',                         'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1082', 'label' => 'Vortrag ideeller Bereich',                           'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1084', 'label' => 'Vortrag Vermögensverwaltung',                        'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1085', 'label' => 'Vortrag Zweckbetriebe Sport',                        'category' => $liab,    'subtype' => null,  'area' => $purpose],
            ['number' => '1086', 'label' => 'Vortrag sonstige Zweckbetriebe',                     'category' => $liab,    'subtype' => null,  'area' => $purpose],
            ['number' => '1087', 'label' => 'Vortrag Geschäftsbetriebe Sport',                    'category' => $liab,    'subtype' => null,  'area' => $economic],
            ['number' => '1088', 'label' => 'Vortrag sonstige Geschäftsbetriebe',                 'category' => $liab,    'subtype' => null,  'area' => $economic],
            ['number' => '1100', 'label' => 'Grundstockvermögen',                                 'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1115', 'label' => 'Kapitalerhaltungsrücklage',                          'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1120', 'label' => 'Sonstige Ergebnisrücklagen',                         'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1125', 'label' => 'Mittelvortrag',                                      'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1140', 'label' => 'Gezeichnetes Kapital',                               'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1145', 'label' => 'Kapitalrücklage',                                    'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1155', 'label' => 'Satzungsmäßige Rücklage',                            'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1160', 'label' => 'Jahresergebnis (Vortrag)',                            'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1170', 'label' => 'Vereinskapital § 58 Nr. 11 AO',                      'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1190', 'label' => 'Gebundene Mittel für Förderzwecke',                  'category' => $liab,    'subtype' => null,  'area' => $ideal],
            ['number' => '1195', 'label' => 'Sonderposten für nicht aufwandswirksam verwendete Spenden', 'category' => $liab, 'subtype' => null, 'area' => $ideal],

            // Rückstellungen
            ['number' => '1200', 'label' => 'Pensionsrückstellungen',                             'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1210', 'label' => 'Steuerrückstellungen',                               'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1220', 'label' => 'Sonstige Rückstellungen',                            'category' => $liab,    'subtype' => null,  'area' => $assetMgr],

            // Verbindlichkeiten
            ['number' => '1320', 'label' => 'Verbindlichkeiten gegenüber Kreditinstituten',       'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1330', 'label' => 'Erhaltene Anzahlungen auf Bestellungen',             'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1340', 'label' => 'Verbindlichkeiten aus Lieferungen und Leistungen',   'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1380', 'label' => 'Verbindlichkeiten für satzungsgemäße Leistungen',    'category' => $liab,    'subtype' => $pay,  'area' => $ideal],
            ['number' => '1385', 'label' => 'Verbindlichkeiten aus erteilten Zusagen',            'category' => $liab,    'subtype' => $pay,  'area' => $ideal],
            ['number' => '1390', 'label' => 'Verbindlichkeiten aus nicht zweckentsprechend verwendeten Mitteln', 'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '1395', 'label' => 'Verbindlichkeiten aus bedingt rückzahlungspflichtigen Spenden', 'category' => $liab, 'subtype' => $pay, 'area' => $ideal],
            ['number' => '1800', 'label' => 'Verbindlichkeiten gegenüber Gesellschafter/Mitglieder', 'category' => $liab, 'subtype' => $pay,  'area' => $ideal],
            ['number' => '1803', 'label' => 'Sonstige Verbindlichkeiten',                         'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1806', 'label' => 'Erhaltene Kautionen',                                'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1809', 'label' => 'Verbindlichkeiten aus Steuern und Abgaben',          'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],
            ['number' => '1812', 'label' => 'Lohnverbindlichkeiten',                              'category' => $liab,    'subtype' => $pay,  'area' => $assetMgr],

            // USt-Konten (Passiva)
            ['number' => '1840', 'label' => 'Umsatzsteuer',                                       'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1845', 'label' => 'Umsatzsteuer 7%',                                    'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1850', 'label' => 'Umsatzsteuer 19%',                                   'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1920', 'label' => 'Umsatzsteuer laufendes Jahr',                        'category' => $liab,    'subtype' => null,  'area' => $assetMgr],
            ['number' => '1990', 'label' => 'Rechnungsabgrenzungsposten passiv',                  'category' => $liab,    'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 2 – Ideeller Bereich
            // ================================================================
            ['number' => '2110', 'label' => 'Echte Mitgliedsbeiträge bis 300 Euro',               'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2120', 'label' => 'Echte Mitgliedsbeiträge 300–1023 Euro',              'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2150', 'label' => 'Aufnahmegebühren bis 300 Euro',                      'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2160', 'label' => 'Aufnahmegebühren 300–1534 Euro',                     'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2170', 'label' => 'Umlagen',                                            'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2300', 'label' => 'Erhaltene nicht steuerbare Zuschüsse',               'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2301', 'label' => 'Zuschüsse von Verbänden',                            'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2302', 'label' => 'Zuschüsse von Behörden',                             'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2303', 'label' => 'Sonstige Zuschüsse',                                 'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2400', 'label' => 'Sonstige Einnahmen ideeller Bereich',                'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2412', 'label' => 'Zuwendungen Dritter (Sponsoren)',                    'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2420', 'label' => 'Steuerfreie Einnahmen gemeinnütziger Vereine',       'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2490', 'label' => 'Zuschreibungen',                                     'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '2500', 'label' => 'Abschreibungen Anlagevermögen',                      'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2501', 'label' => 'Abschreibungen GWG',                                 'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2550', 'label' => 'Anteilige Personalkosten',                           'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2551', 'label' => 'Löhne',                                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2552', 'label' => 'Gehälter',                                           'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2553', 'label' => 'Abgeführte Lohnsteuer',                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2554', 'label' => 'Aufwandsentschädigungen Übungsleiter',               'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2555', 'label' => 'Sozialversicherungsbeiträge',                        'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2556', 'label' => 'Aushilfslöhne',                                     'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2560', 'label' => 'Reisekostenerstattungen',                            'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2661', 'label' => 'Miete und Pacht',                                    'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2663', 'label' => 'Raumnebenkosten',                                    'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2664', 'label' => 'Reparaturen',                                        'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2700', 'label' => 'Kosten der Mitgliederverwaltung',                    'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2701', 'label' => 'Büromaterial',                                       'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2702', 'label' => 'Porto, Telefon',                                     'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2703', 'label' => 'Einzugskosten',                                      'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2751', 'label' => 'Abgaben Landesverband',                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2752', 'label' => 'Abgaben Fachverband',                                'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2753', 'label' => 'Versicherungsbeiträge',                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2801', 'label' => 'Vereinsmitteilungen',                                'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2802', 'label' => 'Geschenke, Jubiläen, Ehrungen',                      'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2803', 'label' => 'Ausbildungskosten',                                  'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2804', 'label' => 'Lehr- und Jugendarbeit',                             'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2810', 'label' => 'Repräsentationskosten',                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2894', 'label' => 'Steuerberatungskosten',                              'category' => $expense, 'subtype' => null,  'area' => $ideal],
            ['number' => '2900', 'label' => 'Sonstige Kosten ideeller Bereich',                   'category' => $expense, 'subtype' => null,  'area' => $ideal],

            // ================================================================
            // KLASSE 3 – Steuerneutrale Posten / Spenden
            // ================================================================
            ['number' => '3210', 'label' => 'Schenkungen',                                        'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3211', 'label' => 'Erbschaften',                                        'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3212', 'label' => 'Vermächtnisse',                                      'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3215', 'label' => 'Sonstige steuerneutrale Einnahmen',                  'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3221', 'label' => 'Geldzuwendungen gegen Zuwendungsbestätigungen',      'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3222', 'label' => 'Geldzuwendungen ohne Zuwendungsbestätigungen',       'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3223', 'label' => 'Sachzuwendungen gegen Zuwendungsbestätigungen',      'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3225', 'label' => 'Aufwandsspenden',                                    'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3230', 'label' => 'Projektbezogene Spenden',                            'category' => $income,  'subtype' => null,  'area' => $ideal],
            ['number' => '3400', 'label' => 'Steuerneutrale Einnahmen Vermögensverwaltung',       'category' => $income,  'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 4 – Vermögensverwaltung
            // ================================================================
            ['number' => '4110', 'label' => 'Mieteinnahmen',                                      'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4111', 'label' => 'Mieteinnahmen Gebäude',                              'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4112', 'label' => 'Mieteinnahmen Räume',                                'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4113', 'label' => 'Mieteinnahmen Plätze/Anlagen',                       'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4120', 'label' => 'Pachteinnahmen',                                     'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4130', 'label' => 'Zinserträge',                                        'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4150', 'label' => 'Zinserträge aus Bankguthaben',                       'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4160', 'label' => 'Dividenden und Gewinnanteile',                       'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4180', 'label' => 'Veräußerungserlöse Anlagevermögen',                  'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4190', 'label' => 'Sonstige Einnahmen Vermögensverwaltung',             'category' => $income,  'subtype' => null,  'area' => $assetMgr],
            ['number' => '4510', 'label' => 'Abschreibungen Vermögensverwaltung',                 'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4520', 'label' => 'Instandhaltung/Reparatur Vermietungsobjekte',        'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4530', 'label' => 'Grundsteuer',                                        'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4540', 'label' => 'Versicherungen Vermögensverwaltung',                 'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4550', 'label' => 'Zinsen und ähnliche Aufwendungen',                   'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4560', 'label' => 'Bankgebühren',                                       'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4712', 'label' => 'Nebenkosten des Geldverkehrs',                       'category' => $expense, 'subtype' => null,  'area' => $assetMgr],
            ['number' => '4900', 'label' => 'Sonstige Kosten Vermögensverwaltung',                'category' => $expense, 'subtype' => null,  'area' => $assetMgr],

            // ================================================================
            // KLASSE 5/6 – Zweckbetriebe
            // ================================================================
            ['number' => '5010', 'label' => 'Eintrittsgelder aus sportlichen Veranstaltungen',    'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5020', 'label' => 'Startgelder',                                        'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5030', 'label' => 'Kurs- und Lehrgangsgebühren Sport',                  'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5040', 'label' => 'Einnahmen aus Sportveranstaltungen',                 'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5060', 'label' => 'Transferentschädigungen für Sportler',               'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5090', 'label' => 'Sonstige Einnahmen Zweckbetrieb Sport',              'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '5520', 'label' => 'Personalkosten Zweckbetrieb Sport',                  'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5540', 'label' => 'Sportbedarf',                                        'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5550', 'label' => 'Kosten sportlicher Veranstaltungen',                 'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5560', 'label' => 'Reisekosten Sport',                                  'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5570', 'label' => 'Schiedsrichtergebühren',                             'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5580', 'label' => 'Meldegebühren und Mannschaftskosten',                'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '5900', 'label' => 'Sonstige Kosten Zweckbetrieb Sport',                 'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '6010', 'label' => 'Eintrittsgelder kulturelle Veranstaltungen',         'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '6020', 'label' => 'Einnahmen aus Wohlfahrtspflege',                     'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '6030', 'label' => 'Kursgebühren (Zweckbetrieb)',                        'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '6040', 'label' => 'Einnahmen aus Bildungsmaßnahmen',                    'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '6090', 'label' => 'Sonstige Einnahmen sonstige Zweckbetriebe',          'category' => $income,  'subtype' => null,  'area' => $purpose],
            ['number' => '6540', 'label' => 'Material-/Sachkosten sonstige Zweckbetriebe',        'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '6550', 'label' => 'Veranstaltungskosten',                               'category' => $expense, 'subtype' => null,  'area' => $purpose],
            ['number' => '6900', 'label' => 'Sonstige Kosten sonstige Zweckbetriebe',             'category' => $expense, 'subtype' => null,  'area' => $purpose],

            // ================================================================
            // KLASSE 7/8 – Wirtschaftlicher Geschäftsbetrieb
            // ================================================================
            ['number' => '7010', 'label' => 'Eintrittsgelder Sport über 35.000 € Umsatz',        'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '7020', 'label' => 'Werbeeinnahmen Sport',                               'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '7030', 'label' => 'Trikotwerbung',                                      'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '7040', 'label' => 'Bandenwerbung',                                      'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '7090', 'label' => 'Sonstige Einnahmen wirtschaftl. Geschäftsbetrieb Sport', 'category' => $income, 'subtype' => null, 'area' => $economic],
            ['number' => '7520', 'label' => 'Personalkosten wirtschaftl. Geschäftsbetrieb Sport', 'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '7560', 'label' => 'Werbekosten Sport',                                  'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '7900', 'label' => 'Sonstige Kosten wirtschaftl. Geschäftsbetrieb Sport', 'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8010', 'label' => 'Einnahmen aus Vereinsgaststätte/Bewirtung',          'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '8020', 'label' => 'Einnahmen aus Altmaterialsammlung',                  'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '8030', 'label' => 'Einnahmen aus Vermietung an Dritte (steuerpflichtig)', 'category' => $income, 'subtype' => null,  'area' => $economic],
            ['number' => '8040', 'label' => 'Einnahmen aus Werbung',                              'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '8050', 'label' => 'Einnahmen aus Warenverkauf',                         'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '8090', 'label' => 'Sonstige Einnahmen wirtschaftl. Geschäftsbetriebe',  'category' => $income,  'subtype' => null,  'area' => $economic],
            ['number' => '8530', 'label' => 'Wareneinkauf Gaststätte/Bewirtung',                  'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8540', 'label' => 'Wareneinkauf sonstige wirtschaftl. Betriebe',        'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8570', 'label' => 'Werbekosten wirtschaftl. Geschäftsbetriebe',         'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8580', 'label' => 'Körperschaftsteuer',                                 'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8590', 'label' => 'Gewerbesteuer',                                      'category' => $expense, 'subtype' => null,  'area' => $economic],
            ['number' => '8900', 'label' => 'Sonstige Kosten wirtschaftl. Geschäftsbetriebe',     'category' => $expense, 'subtype' => null,  'area' => $economic],
        ];
    }
}
