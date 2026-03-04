<?php

declare(strict_types=1);

return [
    'title' => 'Datenschutzerklärung',
    'p_1'   => 'Verantwortlich im Sinne der Datenschutzgesetze:',
    'p_2'   => 'Vertreten durch den Vorstand',

    'sections' => [
        [
            'header' => '1. Allgemeines',
            'body'   => 'Der Schutz Ihrer persönlichen Daten ist uns ein besonderes Anliegen. Wir verarbeiten Ihre Daten ausschließlich auf Grundlage der gesetzlichen Bestimmungen der Datenschutz-Grundverordnung (DSGVO) sowie des Bundesdatenschutzgesetzes (BDSG). Diese Datenschutzerklärung informiert Sie über die wichtigsten Aspekte der Datenverarbeitung im Rahmen unserer Vereinstätigkeit und unserer Webseite.',
        ],
        [
            'header' => '2. Datenverarbeitung auf dieser Webseite',
            'body'   => 'Diese Webseite verarbeitet personenbezogene Daten nur in dem Umfang, der zur Bereitstellung eines sicheren und funktionsfähigen Angebots notwendig ist. Es werden keine Analyse- oder Tracking-Tools eingesetzt. Die Webseite verwendet ausschließlich technisch notwendige Session-Cookies zur Aufrechterhaltung der Anmeldesitzung (Laravel Session Management). Eine Weitergabe an Dritte findet nicht statt.',
        ],
        [
            'header' => '3. Mitgliedsverwaltung',
            'body'   => 'Die von Vereinsmitgliedern übermittelten Daten (Name, Adresse, E-Mail-Adresse, Telefonnummer, Geburtsdatum, Bankverbindung sowie Informationen zur Mitgliedschaft) werden auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO zur Erfüllung des Mitgliedschaftsvertrages verarbeitet. Nach Beendigung der Mitgliedschaft werden persönliche Stammdaten nach einer Aufbewahrungsfrist von 3 Jahren pseudonymisiert. Finanzbezogene Daten (Beitragszahlungen, Buchungen) werden gemäß § 147 AO und § 257 HGB für 10 Jahre aufbewahrt. Alle Datenänderungen werden in einem revisionssicheren Audit-Log protokolliert.',
        ],
        [
            'header' => '4. Newsletter und Veranstaltungsinformationen',
            'body'   => 'Nicht-Mitglieder können sich freiwillig für den Empfang von Vereinsnachrichten und Veranstaltungsinformationen registrieren. Die Verarbeitung der E-Mail-Adresse erfolgt auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO (Einwilligung). Die Einwilligung wird mit Zeitstempel dokumentiert. Sie können Ihre Einwilligung jederzeit durch Klick auf den Abmeldelink in jeder E-Mail widerrufen. Nach der Abmeldung werden Ihre Daten nach einer Übergangsfrist von 30 Tagen vollständig gelöscht. Eine Weitergabe an Dritte findet nicht statt.',
        ],
        [
            'header' => '5. Veranstaltungsanmeldungen',
            'body'   => 'Bei der Anmeldung zu Veranstaltungen werden Name, E-Mail-Adresse sowie optionale Angaben (Telefon, Bemerkungen) auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO verarbeitet. Diese Daten werden ausschließlich zur Durchführung der jeweiligen Veranstaltung verwendet und 30 Tage nach dem Veranstaltungsdatum automatisch gelöscht.',
        ],
        [
            'header' => '6. Hosting und technischer Betrieb',
            'body'   => 'Die Webseite wird auf einem eigenen Server betrieben (Self-Hosted). Die E-Mail-Kommunikation erfolgt über die Server der Strato AG (Deutschland). Dabei werden die datenschutzrechtlichen Anforderungen eingehalten.',
        ],
        [
            'header' => '7. Cookies',
            'body'   => 'Diese Webseite verwendet keine Cookies zu Analyse- oder Trackingzwecken. Es wird ausschließlich ein technisch notwendiges Session-Cookie eingesetzt, das nach dem Schließen des Browsers gelöscht wird. Eine Einwilligung ist hierfür gemäß § 25 Abs. 2 TTDSG nicht erforderlich.',
        ],
        [
            'header' => '8. Datensicherheit',
            'body'   => 'Alle Zugriffe auf personenbezogene Daten im Backend werden in einem Audit-Log protokolliert. Der Zugriff auf Mitgliedsdaten ist auf autorisierte Nutzer (Vorstand, Kassenwart) beschränkt. Dokumente werden verschlüsselt auf einem privaten Storage gespeichert und sind nur über authentifizierte Zugänge abrufbar.',
        ],
        [
            'header' => '9. Ihre Rechte',
            'body'   => 'Sie haben gemäß DSGVO jederzeit das Recht auf Auskunft (Art. 15), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung der Verarbeitung (Art. 18) sowie Datenübertragbarkeit (Art. 20). Eine einmal erteilte Einwilligung können Sie jederzeit mit Wirkung für die Zukunft widerrufen. Zudem steht Ihnen ein Beschwerderecht bei der zuständigen Datenschutzaufsichtsbehörde zu.',
        ],
        [
            'header' => '10. Kontakt',
            'body'   => 'Bei Fragen zum Datenschutz wenden Sie sich bitte an:',
            'email'  => true, // Marker für den E-Mail-Link im Blade
        ],
    ],
];