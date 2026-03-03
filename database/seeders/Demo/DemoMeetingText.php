<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\Membership\Member;

final class DemoMeetingText
{
    public static function meetingsByType(): array
    {
        return [
            'board' => [
                [
                    'title'    => 'Vorstandssitzung – Quartalsplanung',
                    'location' => 'Vereinsheim, Sitzungszimmer',
                    'content' => '',
                    'topics'   => [
                        [
                            'content'      => 'Finanzbericht Q1: Der Kassierer präsentierte den aktuellen Kontostand sowie die Einnahmen und Ausgaben des vergangenen Quartals. Die Finanzlage ist stabil.',
                            'action_items' => [
                                ['description' => 'Jahresabschluss bis Ende des Monats fertigstellen', 'due_days' => 30],
                            ],
                        ],
                        [
                            'content'      => 'Veranstaltungsplanung: Die geplanten Vereinsveranstaltungen für das kommende Halbjahr wurden besprochen und terminlich abgestimmt.',
                            'action_items' => [
                                ['description' => 'Raumreservierungen für alle geplanten Events vornehmen', 'due_days' => 14],
                            ],
                        ],
                        [
                            'content'      => 'Mitgliederentwicklung: Der aktuelle Mitgliederstand wurde vorgestellt. Es wurden Maßnahmen zur Mitgliedergewinnung diskutiert.',
                            'action_items' => [],
                        ],
                    ],
                ],
                [
                    'title'    => 'Außerordentliche Vorstandssitzung',
                    'location' => 'Vereinsheim',
                    'topics'   => [
                        [
                            'content'      => 'Satzungsänderung: Der Vorstand diskutierte eine Anpassung der Vereinssatzung bezüglich der Beitragsstruktur. Eine Mitgliederversammlung soll einberufen werden.',
                            'action_items' => [
                                ['description' => 'Einladungen zur Mitgliederversammlung versenden', 'due_days' => 21],
                            ],
                        ],
                        [
                            'content'      => 'Sponsoring-Anfrage: Eine lokale Firma hat Interesse an einer Vereinspartnerschaft bekundet. Der Vorstand bewertet das Angebot positiv.',
                            'action_items' => [
                                ['description' => 'Sponsoring-Konzept ausarbeiten und dem Unternehmen vorlegen', 'due_days' => 10],
                            ],
                        ],
                    ],
                ],
            ],

            'general_assembly' => [
                [
                    'title'    => 'Ordentliche Jahreshauptversammlung',
                    'location' => 'Vereinsheim, großer Saal',
                    'topics'   => [
                        [
                            'content'      => 'Begrüßung und Feststellung der Beschlussfähigkeit: Der Vorsitzende eröffnete die Versammlung und stellte die Beschlussfähigkeit fest. Alle Punkte der Tagesordnung wurden genehmigt.',
                            'action_items' => [],
                        ],
                        [
                            'content'      => 'Jahresbericht des Vorstands: Der Vorsitzende präsentierte einen umfassenden Rückblick auf die Vereinsaktivitäten des vergangenen Jahres.',
                            'action_items' => [],
                        ],
                        [
                            'content'      => 'Kassenbericht und Entlastung: Der Kassierer legte den Kassenbericht vor. Die Versammlung erteilte dem Vorstand einstimmig die Entlastung.',
                            'action_items' => [
                                ['description' => 'Kassenprüfungsbericht für das Protokoll aufbereiten', 'due_days' => 7],
                            ],
                        ],
                        [
                            'content'      => 'Wahlen: Die turnusmäßigen Vorstandswahlen fanden statt. Der bisherige Vorstand wurde in seinem Amt bestätigt.',
                            'action_items' => [
                                ['description' => 'Wahlergebnis beim Vereinsregister melden', 'due_days' => 30],
                            ],
                        ],
                    ],
                ],
            ],

            'working_group' => [
                [
                    'title'    => 'Arbeitsgruppenmeeting – Festkomitee',
                    'location' => 'Vereinsheim, Nebenraum',
                    'topics'   => [
                        [
                            'content'      => 'Programmplanung Sommerfest: Das Komitee hat das Programm für das Sommerfest ausgearbeitet. Musik, Spiele und Catering wurden besprochen.',
                            'action_items' => [
                                ['description' => 'Angebote von Cateringunternehmen einholen', 'due_days' => 14],
                                ['description' => 'Musikgruppe für den Abend buchen', 'due_days' => 21],
                            ],
                        ],
                        [
                            'content'      => 'Budget: Das verfügbare Budget wurde aufgeteilt. Reserven für unvorhergesehene Ausgaben wurden eingeplant.',
                            'action_items' => [],
                        ],
                    ],
                ],
                [
                    'title'    => 'Planungstreffen – Öffentlichkeitsarbeit',
                    'location' => 'Online / Videokonferenz',
                    'topics'   => [
                        [
                            'content'      => 'Social-Media-Strategie: Die Arbeitsgruppe besprach eine einheitliche Kommunikationsstrategie für die vereinseigenen Social-Media-Kanäle.',
                            'action_items' => [
                                ['description' => 'Redaktionsplan für die nächsten drei Monate erstellen', 'due_days' => 14],
                            ],
                        ],
                        [
                            'content'      => 'Vereinswebsite: Veraltete Inhalte wurden identifiziert. Eine Überarbeitung der wichtigsten Seiten ist geplant.',
                            'action_items' => [
                                ['description' => 'Aktuelle Texte für die Über-uns-Seite verfassen', 'due_days' => 21],
                            ],
                        ],
                    ],
                ],
            ],

            'general' => [
                [
                    'title'    => 'Monatliches Vereinstreffen',
                    'location' => 'Vereinsheim',
                    'topics'   => [
                        [
                            'content'      => 'Aktuelles aus dem Vereinsleben: Der Vorsitzende berichtete über aktuelle Entwicklungen im Verein und informierte über bevorstehende Veranstaltungen.',
                            'action_items' => [],
                        ],
                        [
                            'content'      => 'Verschiedenes: Mitglieder hatten die Gelegenheit, eigene Themen einzubringen. Es wurden allgemeine Fragen zum Vereinsbetrieb besprochen.',
                            'action_items' => [
                                ['description' => 'Informationsblatt für neue Mitglieder aktualisieren', 'due_days' => 14],
                            ],
                        ],
                    ],
                ],
                [
                    'title'    => 'Vereinsmeeting – Rückblick und Ausblick',
                    'location' => 'Vereinsheim, Sitzungszimmer',
                    'topics'   => [
                        [
                            'content'      => 'Rückblick letzte Veranstaltung: Die zuletzt stattgefundene Vereinsveranstaltung wurde evaluiert. Positive Rückmeldungen der Teilnehmer wurden gewürdigt.',
                            'action_items' => [],
                        ],
                        [
                            'content'      => 'Planung nächste Aktivitäten: Ideen für kommende Vereinsaktivitäten wurden gesammelt und priorisiert. Eine Abstimmung unter den Mitgliedern ist geplant.',
                            'action_items' => [
                                ['description' => 'Umfrage zu Aktivitätswünschen an alle Mitglieder versenden', 'due_days' => 7],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function meetingsForType(string $type): array
    {
        return self::meetingsByType()[$type] ?? self::meetingsByType()['general'];
    }

    public static function randomMeetingForType(string $type): array
    {
        $meetings = self::meetingsForType($type);

        return $meetings[array_rand($meetings)];
    }

    public static function attendees(): array
    {
        $boardMembers = Member::getBoardMembers();

        if($boardMembers->isNotEmpty()) {
            return $boardMembers->map(function (Member $member) {
                return [
                    'name' => $member->fullName(),
                    'email' => $member->email,
                    'member_id' => $member->id,
                ];
            })->toArray();
        }

        return [
            ['name' => 'Anna Müller',   'email' => 'anna.mueller@example.com'],
            ['name' => 'Thomas Bauer',  'email' => 'thomas.bauer@example.com'],
            ['name' => 'Maria Schmidt', 'email' => 'maria.schmidt@example.com'],
            ['name' => 'Josef Wagner',  'email' => 'josef.wagner@example.com'],
            ['name' => 'Lisa Huber',    'email' => 'lisa.huber@example.com'],
            ['name' => 'Klaus Fischer', 'email' => 'klaus.fischer@example.com'],
        ];
    }
}