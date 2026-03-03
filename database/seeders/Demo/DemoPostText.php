<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

final class DemoPostText
{
    /**
     * Review-Berichte zu Events – passend zum Event-Typ
     * Key entspricht dem 'type' aus DemoClubText::events()
     */
    public static function reviewsByEventType(): array
    {
        return [
            'carnival' => [
                [
                    'title' => [
                        'de' => 'Rückblick: Ein unvergesslicher Karnevalsabend',
                        'hu' => 'Visszatekintés: Egy felejthetetlen farsangi est',
                        'en' => 'Review: An Unforgettable Carnival Evening',
                    ],
                    'body' => [
                        'de' => '<p>Der diesjährige Karnevalsabend war ein voller Erfolg. Zahlreiche Mitglieder und Gäste kamen zusammen, um gemeinsam zu feiern und zu lachen.</p><p>Die Stimmung war ausgelassen und die Musik ließ niemanden auf seinem Platz. Wir freuen uns schon auf das nächste Mal!</p>',
                        'hu' => '<p>Az idei farsangi est teljes siker volt. Számos tag és vendég gyűlt össze, hogy együtt ünnepeljenek és nevessenek.</p><p>A hangulat felszabadult volt, a zene mindenkit talpon tartott. Már várjuk a következőt!</p>',
                        'en' => '<p>This year\'s carnival evening was a great success. Numerous members and guests came together to celebrate and laugh.</p><p>The atmosphere was exuberant and the music kept everyone on their feet. We are already looking forward to the next one!</p>',
                    ],
                    'label' => 'rückblick',
                ],
                [
                    'title' => [
                        'de' => 'Karnevals-Highlights: Das war der Abend',
                        'hu' => 'Farsangi pillanatok: Ilyen volt az est',
                        'en' => 'Carnival Highlights: A Look Back at the Evening',
                    ],
                    'body' => [
                        'de' => '<p>Kostüme, Tanz und jede Menge gute Laune – unser Karnevalsabend hat wieder alle begeistert. Besonders die Kostümshow sorgte für großes Gelächter und Applaus.</p><p>Herzlichen Dank an alle Helferinnen und Helfer, die diesen Abend möglich gemacht haben.</p>',
                        'hu' => '<p>Jelmezek, tánc és rengeteg jókedv – a farsangi estünk ismét mindenkit elbűvölt. A jelmezbemutató különösen nagy nevetést és tapsot váltott ki.</p><p>Köszönet minden önkéntesnek, akik lehetővé tették ezt az estét.</p>',
                        'en' => '<p>Costumes, dancing and plenty of good cheer – our carnival evening delighted everyone once again. The costume show in particular prompted great laughter and applause.</p><p>Many thanks to all the helpers who made this evening possible.</p>',
                    ],
                    'label' => null,
                ],
            ],

            'model' => [
                [
                    'title' => [
                        'de' => 'Modellbau-Stammtisch: Kreativität und Austausch',
                        'hu' => 'Modellező klubest: Kreativitás és tapasztalatcsere',
                        'en' => 'Model Building Meetup: Creativity and Exchange',
                    ],
                    'body' => [
                        'de' => '<p>Beim letzten Modellbau-Stammtisch zeigten unsere Mitglieder wieder beeindruckende Projekte. Von historischen Flugzeugen bis hin zu detailgetreuen Fahrzeugmodellen war alles dabei.</p><p>Die Fachgespräche und gegenseitigen Tipps machen diese Treffen so wertvoll. Wir sehen uns beim nächsten Stammtisch!</p>',
                        'hu' => '<p>A legutóbbi modellező klubesten tagjaink ismét lenyűgöző projekteket mutattak be. Történelmi repülőgépektől részletgazdag járműmodellekig minden megtalálható volt.</p><p>A szakmai beszélgetések és kölcsönös tippek teszik értékessé ezeket a találkozókat. Találkozunk a következő klubesten!</p>',
                        'en' => '<p>At the last model building meetup, our members once again showcased impressive projects. From historic aircraft to detailed vehicle models, everything was represented.</p><p>The technical discussions and mutual tips are what make these meetings so valuable. See you at the next one!</p>',
                    ],
                    'label' => 'bericht',
                ],
            ],

            'general' => [
                [
                    'title' => [
                        'de' => 'Vereinsabend: Gemeinschaft im Mittelpunkt',
                        'hu' => 'Egyesületi est: A közösség a középpontban',
                        'en' => 'Club Evening: Community at the Heart',
                    ],
                    'body' => [
                        'de' => '<p>Unser letzter Vereinsabend stand ganz im Zeichen des Miteinanders. Alte und neue Mitglieder kamen ins Gespräch, tauschten Erfahrungen aus und planten gemeinsam die nächsten Aktivitäten.</p><p>Solche Abende sind das Herzstück unseres Vereinslebens. Wir freuen uns auf alle, die beim nächsten Mal dabei sind!</p>',
                        'hu' => '<p>A legutóbbi egyesületi estünk a közösség jegyében telt. Régi és új tagok kerültek szóba, tapasztalatokat cseréltek és közösen tervezték a következő programokat.</p><p>Az ilyen esték az egyesületi életünk szívét jelentik. Várjuk mindazokat, akik legközelebb is csatlakoznak!</p>',
                        'en' => '<p>Our last club evening was all about togetherness. Old and new members got talking, shared experiences and planned upcoming activities together.</p><p>Evenings like this are the heart of our club life. We look forward to seeing everyone next time!</p>',
                    ],
                    'label' => null,
                ],
                [
                    'title' => [
                        'de' => 'Ein gelungener Abend – Danke an alle Teilnehmer',
                        'hu' => 'Egy sikeres est – Köszönet minden résztvevőnek',
                        'en' => 'A Successful Evening – Thank You to All Participants',
                    ],
                    'body' => [
                        'de' => '<p>Was für ein Abend! Die Beteiligung war so groß wie selten, und die Gespräche reichten von Vereinsneuigkeiten bis zu persönlichen Erlebnissen.</p><p>Wir danken allen, die gekommen sind und zum guten Gelingen beigetragen haben.</p>',
                        'hu' => '<p>Micsoda est! A részvétel olyan nagy volt, mint ritkán, és a beszélgetések az egyesületi hírektől a személyes élményekig terjedtek.</p><p>Köszönünk mindenkinek, aki eljött és hozzájárult a sikerhez.</p>',
                        'en' => '<p>What an evening! Attendance was higher than usual, and conversations ranged from club news to personal stories.</p><p>We thank everyone who came and contributed to the success of the event.</p>',
                    ],
                    'label' => 'danke',
                ],
            ],
        ];
    }

    /**
     * Gibt alle Reviews für einen bestimmten Event-Typ zurück
     */
    public static function reviewsForType(string $eventType): array
    {
        return self::reviewsByEventType()[$eventType] ?? self::reviewsByEventType()['general'];
    }

    /**
     * Gibt einen zufälligen Review für einen Event-Typ zurück
     */
    public static function randomReviewForType(string $eventType): array
    {
        $reviews = self::reviewsForType($eventType);

        return $reviews[array_rand($reviews)];
    }
}
