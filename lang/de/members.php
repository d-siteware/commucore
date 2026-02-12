<?php

declare(strict_types=1);


return [
    'title' => 'Mitgliederübersicht',
    'header' => 'Hier finden Sie eine sortierbare Übersicht aller Mitglieder. Im Untermenü können Mitglieder bearbeitet, Zahlungen erfasst oder Mitglieder als inaktiv markiert werden. Letzteres ersetzt das Löschen des Eintrags.',
    'table' => [
        'header' => [
            'name' => 'Name',
            'phone' => 'Mobilnummer',
            'status' => 'Status',
            'fee_status' => 'Beitragsstatus',
            'birthday' => 'Geburtstag',
        ],
    ],
    'con' => [
        'men' => [
            'edit' => 'Bearbeiten',
            'payment' => 'Zahlung erfassen',
            'delete' => 'Löschen',
        ],
    ],
    'widget' => [
        'birthday' => [
            'card' => [
                'table' => [
                    'header' => [
                        'member' => 'Mitglied',
                        'birthday' => 'Geburtsdatum',
                        'newage' => 'Alter',
                    ],
                ],
                'heading' => 'Anstehende Geburtstage für :name',
            ],
        ],
    ],
    'fee-type' => [
        'free' => 'Beitragsbefreit',
        'standard' => 'Standardbeitrag',
        'discounted' => 'Ermäßigter Beitrag',
    ],
    'fee_type' => 'Beitragsstatus',
    'apply' => [
        'discount' => [
            'label' => 'Ermäßigten Mitgliedsbeitrag beantragen',
            'reason' => [
                'label' => 'Grund für die Ermäßigung',
            ],
        ],
        'fee' => [
            'text' => 'Ich wurde über den monatlichen Mitgliedsbeitrag von :sum EUR informiert und verpflichte mich zur Zahlung.',
            'label' => 'Bezahlende Mitglieder müssen monatlich einen Betrag von :sum EUR zahlen. Mitglieder über 75 Jahre sind von der Beitragspflicht befreit.',
            'payment' => [
                'banktt' => 'Der fällige Beitrag ist auf das angegebene Konto zu zahlen.',
                'paypals' => 'Der Beitrag kann auf ein der PayPal-Konten gesendet werden. Bitte als Methode "Freunde Geld senden" wählen, da sonst 1.8% als Gebühr seitens PayPal abgezogen werden.',
                'paypal' => 'Der Beitrag kann auf das PayPal-Konto :iban gesendet werden. Bitte als Methode "Freunde Geld senden" wählen, da sonst 1.8% als Gebühr seitens abgezogen werden.',
            ],
        ],
        'full_fee' => [
            'label' => 'Bezahlende Mitglieder müssen monatlich einen Betrag von :sum EUR zahlen.',
        ],
        'discounted_fee' => [
            'label' => 'Mitglieder können einen reduzierten monatlichen Beitrag von :sum EUR beantragen.',
        ],
        'free_fee' => [
            'label' => 'Mitglieder über :age Jahren sind von der Beitragspflicht befreit.',
        ],
        'email' => [
            'none' => 'Ich habe keine E-Mail-Adresse!',
            'without' => [
                'text' => 'Wenn Sie keine E-Mail-Adresse haben, können Sie dieses Formular ausdrucken, unterschreiben und an die folgende Adresse per Post senden:',
            ],
            'benefits' => 'Mitglieder mit einer E-Mail-Adresse erhalten automatisch Benachrichtigungen über Veranstaltungen und haben Zugang zum Schwarzen Brett.',
            'note' => [
                'header' => 'Wichtig!',
                'content' => 'Für die Übermittlung über das Webprogramm müssen Sie Ihre E-Mail-Adresse angeben. Wenn Sie keine E-Mail-Adresse haben, wählen Sie den Postdienst.',
            ],
        ],
        'checkAndSubmit' => 'Informationen überprüfen und Formular absenden',
        'printAndSubmit' => 'Formular drucken',
        'title' => 'Antrag auf Mitgliedschaft bei der Ungarischen Kolonie Berlin e. V.',
        'text' => 'Wir freuen uns, dass Sie Mitglied der Ungarischen Kolonie Berlin e. V. werden möchten.',
        'process' => 'Die Aufnahme erfolgt nach folgendem Verfahren:',
        'step1' => [
            'label' => 'Schritt 1',
            'text' => 'Füllen Sie als ersten Schritt das folgende Formular aus.',
        ],
        'via' => [
            'web' => 'Über das Web versenden',
            'postal' => 'Postalischer Versand',
        ],
        'step2' => [
            'label' => 'Schritt 2',
            'text' => 'Überprüfen Sie Ihre Angaben',
        ],
        'click' => [
            'button' => 'Klicken Sie auf den Button',
            'checkbox' => 'Klicken Sie auf das Kästchen',
        ],
        'step3a' => [
            'label' => 'Schritt 3a',
            'text' => 'Füllen Sie als ersten Schritt das folgende Formular aus.',
        ],
        'step3b' => [
            'label' => 'Schritt 3b',
            'text' => '[DE] Kattintson a „Űrlap nyomtatása” gombra.',
        ],
        'step4a' => [
            'label' => 'Schritt 4a',
            'text' => 'Sie erhalten eine E-Mail vom System mit einem einmaligen Bestätigungslink.',
        ],
        'step4b' => [
            'label' => 'Schritt 4b',
            'text' => 'Klicken Sie auf die Schaltfläche [Formular drucken], um eine PDF-Version des Formulars zu erstellen.',
        ],
        'step5a' => [
            'label' => 'Schritt 5a',
            'text' => 'Durch Klicken auf den Link bestätigen Sie, dass die Registrierung tatsächlich von Ihnen stammt.',
        ],
        'step5b' => [
            'label' => 'Schritt 5b',
            'text' => 'Drucken Sie das Formular aus, unterschreiben Sie es und senden Sie es an die auf dem Formular angegebene Adresse.',
        ],
        'step6' => [
            'label' => 'Schritt 6',
            'text' => 'Wir prüfen Ihre Angaben und nehmen persönlich Kontakt mit Ihnen auf, falls weitere Informationen benötigt werden.',
        ],
        'step7' => [
            'label' => 'Schritt 7',
            'text' => 'Abschließend wird über Ihre Aufnahme in das Leitungsteam entschieden, und Sie erhalten auf dem von Ihnen gewählten Weg eine Benachrichtigung per E-Mail oder Post.',
        ],
        'submission' => [
            'success' => [
                'head' => 'Erfolg!',
                'msg' => 'Wir haben Ihre Bewerbung erhalten und prüfen sie. Vielen Dank!',
            ],
            'failed' => [
                'head' => 'Fehler!',
                'msg' => 'Leider ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
            ],
        ],
        'print' => [
            'title' => 'Bewerbung um die Mitgliedschaft bei der Ungarischen Kolonie Berlin e. V.',
            'greeting' => 'Sehr geehrte Damen und Herren!',
            'text' => 'Hiermit bewerbe ich mich um die Mitgliedschaft bei der Ungarischen Kolonie Berlin e. V.',
            'regards' => 'Mit freundlichen Grüßen',
            'overview' => [
                'person' => 'Über mich',
                'contact' => 'Meine Kontaktinformationen',
            ],
            'filename' => 'Bewerbung_Mitgliedschaft_Ungarische_Kolonie_Berlin_mid-:id:tm.pdf',
        ],
    ],
    'birth_date' => 'Geburtsdatum',
    'birth_place' => 'Geburtsort',
    'name' => 'Nachname',
    'first_name' => 'Vorname',
    'email' => 'E-Mail',
    'phone' => 'Telefon',
    'mobile' => 'Mobilnummer',
    'address' => 'Adresse',
    'zip' => 'Postleitzahl',
    'city' => 'Stadt',
    'country' => 'Land',
    'locale' => 'Bevorzugte Sprache',
    'gender' => 'Geschlecht',
    'type' => [
        'standard' => 'Mitglied',
        'applicant' => 'Anwärter',
        'board' => 'Vorstand',
        'advisor' => 'Beirat',
    ],
    'linked_user' => 'Verknüpft mit Benutzerkonto',
    'unlink_user' => 'Verknüpfung aufheben',
    'left_at' => 'Austrittsdatum',
    'section' => [
        'admins' => 'Vom Vorstand auszufüllen',
        'person' => 'Person',
        'address' => 'Anschrift',
        'phone' => 'Telefon',
        'fees' => 'Beitrag',
        'payments' => 'Zahlungen',
        'deduction' => 'Ermäßigung',
        'email' => 'E-Mail Adresse',
    ],
    'update' => [
        'success' => [
            'title' => 'Erfolg',
            'content' => 'Die Mitgliedsdaten wurden erfolgreich aktualisiert.',
        ],
    ],
    'date' => [
        'applied_at' => 'Mitgliedschaft beantragt am',
        'verified_at' => 'E-Mail verifiziert am',
        'entered_at' => 'Mitgliedschaft bestätigt am',
        'left_at' => 'Ausgetreten am',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => 'Verifizierungs-Erinnerung senden',
        ],
        'addMember' => 'neu anlegen',
        'sendAcceptanceMail' => [
            'label' => 'Antrag annehmen und E-Mail senden',
        ],
        'sendAcceptance' => [
            'label' => 'Antrag annehmen',
        ],
        'setEnteredAt' => [
            'label' => 'Angenommen am',
        ],
        'inviteAsUser' => [
            'label' => 'Mitglied als Benutzer einladen',
        ],
        'cancelMembership' => [
            'label' => 'Mitgliedschaft kündigen',
        ],
    ],
    'accordion' => [
        'optionals' => [
            'label' => 'Optionale Angaben',
        ],
    ],
    'appliance_received' => [
        'mail' => [
            'subject' => 'Ihr Mitgliedsantrag ist eingegangen!',
            'greeting' => 'Hallo :name,',
            'text' => 'wir haben Ihren Mitgliedsantrag erhalten und bedanken uns für Ihr Interesse an unserer Gemeinschaft. Wir werden Ihren Antrag schnellstmöglich prüfen und uns bei Ihnen melden.',
        ],
    ],
    'cancel' => [
        'modal' => [
            'title' => 'Mitgliedschaft kündigen',
            'text' => 'Bitte bestätigen Sie die Kündigung der Mitgliedschaft.',
        ],
        'confirm_text_input' => [
            'label' => 'Zur Bestätigung bitte den Nachnamen eingeben',
        ],
        'btn' => [
            'final' => [
                'label' => 'Mitglied endgültig kündigen',
            ],
        ],
    ],
    'optional-data' => [
        'text' => 'Hier können weitere Angaben gemacht werden.',
    ],
    'familystatus' => [
        'label' => 'Familienstand',
        'single' => 'Ledig',
        'married' => 'Verheiratet',
        'divorced' => 'Geschieden',
        'n_a' => 'Keine Angabe',
    ],
    'show' => [
        'title' => 'Mitgliedsübersicht: :name',
        'created_at' => 'Erstellt am',
        'updated_at' => 'Zuletzt bearbeitet am',
        'about' => 'Persönliche Angaben',
        'membership' => 'Mitgliedschaft',
        'payments' => 'Zahlungen',
        'store' => 'Speichern',
        'fee_msg' => [
            'exempted' => 'Beitragsbefreit',
            'paid' => 'Beitrag bezahlt',
        ],
        'invitation_sent' => 'Einladung wurde versendet',
        'member' => [
            'reactivate' => 'Mitglied reaktivieren',
        ],
        'select_user' => 'Benutzer auswählen',
        'empty_user_list' => 'Keine Benutzer gefunden',
        'heading' => 'Mitglied Daten zeigen',
        'attached' => [
            'success' => [
                'head' => 'Erfolg!',
                'msg' => 'Die Verknüpfung des Benutzers :name wurde erfolgreich durchgeführt.',
            ],
            'placeholder' => 'Benutzer auswählen',
            'failed' => [
                'head' => 'Fehler!',
                'msg' => 'Der Benutzer konnte nicht verknüpft werden.',
            ],
        ],
        'detached' => [
            'success' => [
                'head' => 'Erfolg!',
                'msg' => 'Die Verknüpfung des Benutzers :name wurde erfolgreich entfernt.',
            ],
        ],
    ],
    'register' => [
        'title' => 'Passwort für die Registrierung festlegen',
        'page_title' => 'Registrierung abschließen',
        'password_requirements' => 'Das Passwort sollte folgende Kriterien erfüllen:',
        'password' => 'Passwort',
        'password_confirm' => 'Passwort bestätigen',
        'submit' => 'Registrierung abschließen',
        'checkLength' => 'Mindestens 8 Zeichen',
        'checkCapital' => 'Mindestens ein Großbuchstabe',
        'checkNumbers' => 'Mindestens eine Zahl',
        'checkSpecial' => 'Mindestens ein Sonderzeichen (!"$§%(){}[])',
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'Neue Mitgliedsanträge',
            'empty_search' => 'Kein passender Eintrag',
            'empty_list' => 'Keine offenen Anträge',
            'confirm' => [
                'deletion' => [
                    'title' => 'Erfolg',
                    'text' => 'Die ausgewählten Anträge wurden gelöscht',
                ],
            ],
            'options' => [
                'label' => 'Optionen',
                'deletion' => [
                    'confirm' => 'Bitte bestätigen Sie die Löschung der ausgewählten Anträge!',
                    'btn' => [
                        'label' => 'Löschen',
                    ],
                ],
                'edit' => [
                    'btn' => [
                        'label' => 'Bearbeiten',
                    ],
                ],
            ],
            'search' => [
                'label' => 'Anträge durchsuchen',
            ],
            'tab' => [
                'header' => [
                    'from' => 'Datum',
                    'name' => 'Name',
                ],
            ],
        ],
    ],
    'application' => [
        'errors' => [
            'name-reqipred' => 'Bitte den Nachnamen angeben',
        ],
    ],
    'index' => [
        'search-placeholder' => 'Suche',
    ],
    'create' => [
        'title' => 'Mitglied anlegen',
        'message' => [
            'success' => 'Mitglied erfolgreich angelegt',
            'fail' => 'Mitglied konnte nicht angelegt werden. Admin nach Log Einträgen fragen!',
        ],
    ],
    'backend' => [
        'create' => [
            'heading' => 'Neues Mitglied anlegen',
            'btn' => [
                'submit' => 'Mitglied erfassen',
            ],
        ],
    ],
    'fees' => [
        // Page header
        'overview_title' => 'Übersicht Mitgliedsbeiträge',
        'year' => 'Jahr',

        // Filter & Search
        'search_member_placeholder' => 'Mitglied suchen...',
        'show_inactive' => 'Inaktive anzeigen',
        'pdf_export' => 'PDF Export',
        'csv_export' => 'CSV Export',

        // Summary cards
        'members' => 'Mitglieder',
        'paid' => 'Bezahlt',
        'open' => 'Offen',
        'transactions' => 'Transaktionen',
        'payments' => 'Zahlungen',

        // Table columns
        'member' => 'Mitglied',
        'type' => 'Typ',
        'date' => 'Datum',
        'status' => 'Status',
        'receipt' => 'Beleg',

        // Status badges
        'status_booked' => 'gebucht',
        'status_submitted' => 'eingereicht',

        // Actions
        'send' => 'Senden',
    ],
];
