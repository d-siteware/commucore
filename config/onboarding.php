<?php

declare(strict_types=1);

use App\Enums\OnboardingPriority;

return [

    /*
    |--------------------------------------------------------------------------
    | Onboarding-Checklist-Module
    |--------------------------------------------------------------------------
    |
    | Jeder Eintrag beschreibt einen Prüfpunkt der Einrichtungs-Checkliste.
    | Die tatsächliche Prüflogik (ob der Punkt erfüllt ist) lebt weiterhin
    | in App\Services\OnboardingStatusService::resolve() — diese Config
    | bestimmt nur, WIE der Punkt in der UI dargestellt wird und WELCHE
    | Priorität er hat.
    |
    | status_key  => Schlüssel aus OnboardingStatusService::getStatus()
    | label       => Anzeigetext (Übersetzungs-Key empfohlen)
    | priority    => OnboardingPriority::Critical oder ::Important
    | route       => Routenname für "Zum Modul"-Link (optional)
    | tutorial    => externe Tutorial-URL (optional)
    | activity    => true, wenn dieser Punkt zur Aktivitäten-Sektion gehört
    |                (erscheint erst, wenn keine Critical-Punkte mehr offen sind)
    |
    */

    'sections' => [

        'legal' => [
            'label' => 'Rechtliche Grundlagen',
            'items' => [
                [
                    'status_key' => 'has_organization_data',
                    'label' => 'Vereinsdaten vervollständigen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'settings',
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_statute',
                    'label' => 'Satzung eintragen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'settings',
                    'tutorial' => 'https://docs.commu-core.com/tutorials/satzung-hinterlegen',
                ],
                [
                    'status_key' => 'has_board_member',
                    'label' => 'Vorstand bestimmen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'backend.members.index',
                    'tutorial' => null,
                ],
            ],
        ],

        'finance' => [
            'label' => 'Finanzen',
            'items' => [
                [
                    'status_key' => 'has_account',
                    'label' => 'Zahlungskonto einrichten',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'accounts.create',
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_datev_berater_nr',
                    'label' => 'DATEV-Beraternummer hinterlegen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'settings',
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_datev_mandant_nr',
                    'label' => 'DATEV-Mandantennummer hinterlegen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'settings',
                    'tutorial' => null,
                ],
            ],
        ],

        'members' => [
            'label' => 'Mitglieder & Rollen',
            'items' => [
                [
                    'status_key' => 'has_min_members',
                    'label' => 'Weitere Mitglieder anlegen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'backend.members.create',
                    'tutorial' => 'https://docs.commu-core.com/tutorials/mitglied-erstellen',
                ],
                [
                    'status_key' => 'has_all_roles_assigned',
                    'label' => 'Rollen an Mitglieder zuweisen',
                    'priority' => OnboardingPriority::Critical,
                    'route' => 'backend.members.roles',
                    'tutorial' => null,
                ],
            ],
        ],

        'completion' => [
            'label' => 'Vervollständigung',
            'items' => [
                [
                    'status_key' => 'has_fiscal_year',
                    'label' => 'Geschäftsjahr anlegen',
                    'priority' => OnboardingPriority::Important,
                    'route' => 'fiscal-years.index',
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_logo',
                    'label' => 'Logo hochladen',
                    'priority' => OnboardingPriority::Important,
                    'route' => 'settings',
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_about_us',
                    'label' => 'Über-uns-Text schreiben',
                    'priority' => OnboardingPriority::Important,
                    'route' => 'settings',
                    'tutorial' => null,
                ],
            ],
        ],

        'activities' => [
            'label' => 'Aktivitäten',
            'activity' => true,
            'items' => [
                [
                    'status_key' => 'has_venue',
                    'label' => 'Ersten Veranstaltungsort anlegen',
                    'priority' => OnboardingPriority::Important,
                    'route' => null,
                    'tutorial' => null,
                ],
                [
                    'status_key' => 'has_event',
                    'label' => 'Erste Veranstaltung erstellen',
                    'priority' => OnboardingPriority::Important,
                    'route' => 'backend.events.create',
                    'tutorial' => null,
                ],
            ],
        ],

    ],

];
