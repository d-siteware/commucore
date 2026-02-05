<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

final class DemoVenueText
{
    /**
     * Jeder Eintrag = ein Veranstaltungsort
     */
    public static function venues(): array
    {
        return [
            [
                'name' => [
                    'de' => 'Vereinshaus Grünspechtweg',
                    'hu' => 'Grünspechtweg Egyesületi Ház',
                    'en' => 'Grünspechtweg Clubhouse',
                ],
                'address' => 'Grünspechtweg 19',
                'city' => 'Berlin',
                'zipcode' => '13469',
                'country' => 'Deutschland',
                'excerpt' => [
                    'de' => 'Gemütliches Vereinshaus für alle Vereinsaktivitäten.',
                    'hu' => 'Kényelmes egyesületi ház minden tevékenységhez.',
                    'en' => 'Cozy clubhouse for all club activities.',
                ],
                'description' => [
                    'de' => 'Unser Vereinsheim bietet ausreichend Platz für Versammlungen, Feiern und Workshops.',
                    'hu' => 'Egyesületi házunk elegendő helyet biztosít összejövetelekhez, rendezvényekhez és workshopokhoz.',
                    'en' => 'Our clubhouse provides ample space for meetings, celebrations, and workshops.',
                ],
            ],

            [
                'name' => [
                    'de' => 'Kulturzentrum Altstadt',
                    'hu' => 'Óvárosi Kulturális Központ',
                    'en' => 'Old Town Cultural Center',
                ],
                'address' => 'Marktstraße 5',
                'zipcode' => '13469',
                'city' => 'Berlin',
                'country' => 'Deutschland',
                'excerpt' => [
                    'de' => 'Zentrale Lage für Kultur- und Vereinsveranstaltungen.',
                    'hu' => 'Központi helyszín kulturális és egyesületi rendezvényekhez.',
                    'en' => 'Central location for cultural and club events.',
                ],
                'description' => [
                    'de' => 'Ideal für größere Veranstaltungen und öffentliche Events mit guter Anbindung.',
                    'hu' => 'Ideális nagyobb rendezvényekhez és nyilvános eseményekhez, jó közlekedéssel.',
                    'en' => 'Ideal for larger events and public gatherings with good transport links.',
                ],
            ],

            [
                'name' => [
                    'de' => 'Bastelhalle Modellbau',
                    'hu' => 'Modellező Műhely',
                    'en' => 'Model Building Workshop',
                ],
                'address' => 'Werkstraße 12',
                'city' => 'Berlin',
                'zipcode' => '10598',
                'country' => 'Deutschland',
                'excerpt' => [
                    'de' => 'Perfekter Ort für Modellbau-Workshops und Stammtische.',
                    'hu' => 'Tökéletes hely modellező workshopokhoz és klubestekhez.',
                    'en' => 'Perfect place for model building workshops and meetups.',
                ],
                'description' => [
                    'de' => 'Mit allen nötigen Werkzeugen und ausreichend Platz für Bastelprojekte.',
                    'hu' => 'Minden szükséges eszközzel és elegendő hellyel a modellezéshez.',
                    'en' => 'Equipped with all necessary tools and plenty of space for projects.',
                ],
            ],

            [
                'name' => [
                    'de' => 'Festsaal Rosenweg',
                    'hu' => 'Rosenweg Rendezvényterem',
                    'en' => 'Rosenweg Event Hall',
                ],
                'address' => 'Rosenweg 7',
                'city' => 'Berlin',
                'zipcode' => '11056',
                'country' => 'Deutschland',
                'excerpt' => [
                    'de' => 'Großer Saal für Karnevals- und Vereinsfeiern.',
                    'hu' => 'Nagy terem farsangi és egyesületi rendezvényekhez.',
                    'en' => 'Large hall for carnival and club celebrations.',
                ],
                'description' => [
                    'de' => 'Ideal für gesellige Abende, Feiern und musikalische Events.',
                    'hu' => 'Ideális társas estekhez, ünnepségekhez és zenei rendezvényekhez.',
                    'en' => 'Ideal for social evenings, celebrations, and musical events.',
                ],
            ],
        ];
    }

    /**
     * Gibt einen zufälligen Venue zurück
     */
    public static function randomVenue(): array
    {
        return self::venues()[array_rand(self::venues())];
    }
}
