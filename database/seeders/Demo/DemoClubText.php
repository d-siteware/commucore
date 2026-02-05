<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

final class DemoClubText
{
    /**
     * Jede Struktur = EIN Event
     */
    public static function events(): array
    {
        return [
            [
                'type' => 'carnival',
                'title' => [
                    'de' => 'Karnevalsabend',
                    'hu' => 'Farsangi est',
                    'en' => 'Carnival Evening',
                ],
                'excerpt' => [
                    'de' => 'Fröhlicher Vereinsabend in karnevalistischer Atmosphäre.',
                    'hu' => 'Vidám egyesületi est farsangi hangulatban.',
                    'en' => 'A cheerful club evening with a carnival atmosphere.',
                ],
                'description' => [
                    'de' => 'Gemeinsames Feiern mit Musik, Gesprächen und guter Stimmung.',
                    'hu' => 'Közös ünneplés zenével, beszélgetéssel és jó hangulattal.',
                    'en' => 'Celebrating together with music, conversation and a great atmosphere.',
                ],
            ],

            [
                'type' => 'model',
                'title' => [
                    'de' => 'Modellbau-Stammtisch',
                    'hu' => 'Modellező klubest',
                    'en' => 'Model Building Meetup',
                ],
                'excerpt' => [
                    'de' => 'Treffen für Modellbau-Begeisterte.',
                    'hu' => 'Találkozó a modellezés iránt érdeklődőknek.',
                    'en' => 'Meetup for model building enthusiasts.',
                ],
                'description' => [
                    'de' => 'Gemeinsames Basteln, Fachgespräche und Präsentation aktueller Projekte.',
                    'hu' => 'Közös alkotás, szakmai beszélgetések és projektek bemutatása.',
                    'en' => 'Collaborative building, technical discussions and project presentations.',
                ],
            ],

            [
                'type' => 'general',
                'title' => [
                    'de' => 'Vereinsabend',
                    'hu' => 'Egyesületi est',
                    'en' => 'Club Evening',
                ],
                'excerpt' => [
                    'de' => 'Offenes Treffen für Mitglieder und Gäste.',
                    'hu' => 'Nyitott találkozó tagok és vendégek számára.',
                    'en' => 'Open meeting for members and guests.',
                ],
                'description' => [
                    'de' => 'Geselliges Beisammensein mit Austausch und Gesprächen.',
                    'hu' => 'Közösségi együttlét beszélgetéssel és tapasztalatcserével.',
                    'en' => 'Social gathering with conversation and exchange.',
                ],
            ],
        ];
    }

    public static function randomEvent(): array
    {
        return self::events()[array_rand(self::events())];
    }
}
