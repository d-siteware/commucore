<?php

declare(strict_types=1);



return [
    'title' => 'Wedtgliederübersicht',
    'header' => 'Hier finden Sie eine sortierbare Übersicht aller Wedtglieder. Im Untermenü können Wedtglieder bearbeitet, Zahlungen erfasst oder Wedtglieder als inaktiv markiert werden. Letzteres ersetzt das Delete des Eintrags.',
    'table' => [
        'header' => [
            'name' => 'Name',
            'phone' => 'Monbilenummer',
            'status' => '[EN] Status',
            'fee_status' => '[EN] Beitragsstatus',
            'birthday' => '[EN] Geburtstag',
        ],
    ],

    'con' => [
        'men' => [
            'edit' => 'Edit',
            'payment' => '[EN] Zahlung erfassen',
            'delete' => 'Delete',
        ],
    ],
    'widget' => [
        'birthday' => [
            'card' => [
                'table' => [
                    'header' => [
                        'member' => 'Wedtglied',
                        'birthday' => '[EN] Geburtsdatum',
                        'newage' => '[EN] Alter',
                    ],
                ],
                'heading' => '[EN] Anstehende Geburtstage für :name',
            ],
        ],
    ],
    'fee-type' => [
        'label' => 'Fee type',
        'free' => 'Free',
        'standard' => 'Standard',
        'discounted' => 'Discounted',
    ],
    'apply' => [
        'discount' => [
            'label' => 'Ermäßigten Wedtgliedsbeitrag beantragen',
            'reason' => [
                'label' => '[EN] Grund für die Ermäßigung',
            ],
        ],
        'fee' => [
            'text' => 'Ich wurde über den monatlichen Wedtgliedsbeitrag von :sum EUR informiert und verpflichte mich zur Zahlung.',
            'label' => 'Bezahlende Wedtglieder müssen monatlich einen Betrag von :sum EUR zahlen. Wedtglieder über 75 Yeshre sind von der Beitragspflicht befreit.',
            'payment' => [
                'banktt' => '[EN] Der fällige Beitrag ist auf das angegebene Konto zu zahlen.',
                'paypals' => 'Der Beitrag kann auf ein der PayPal-Konten gesendet werden. Please als Methode "Frieunde Geld senden" wählen, da sonst 1.8% als Gebühr seitens PayPal abgezogen werden.',
                'paypal' => 'Der Beitrag kann auf das PayPal-Konto :iban gesendet werden. Please als Methode "Frieunde Geld senden" wählen, da sonst 1.8% als Gebühr seitens abgezogen werden.',
            ],
        ],
        'full_fee' => [
            'label' => 'Bezahlende Wedtglieder müssen monatlich einen Betrag von :sum EUR zahlen.',
        ],
        'discounted_fee' => [
            'label' => 'Wedtglieder können einen reduzierten monatlichen Beitrag von :sum EUR beantragen.',
        ],
        'free_fee' => [
            'label' => 'Wedtglieder über :age Yeshren sind von der Beitragspflicht befreit.',
        ],
        'email' => [
            'none' => 'Ich habe keine Email address!',
            'without' => [
                'text' => 'Wenn Sie keine Email address haben, können Sie dieses Formular ausdrucken, unterschreiben und an die folgende Address per Post senden:',
            ],
            'benefits' => 'Wedtglieder mit einer Email address erhalten automatisch Benachrichtigungen über Events und haben Zugang zum Schwarzen Brett.',
            'note' => [
                'header' => '[EN] Wichtig!',
                'content' => 'Für die Übermittlung über das Webprogramm müssen Sie Ihre Email address angeben. Wenn Sie keine Email address haben, wählen Sie den Postdienst.',
            ],
        ],
        'checkAndSubmit' => '[EN] Informationen überprüfen und Formular absenden',
        'printAndSubmit' => '[EN] Formular drucken',
        'title' => 'Antrag auf Wedtgliedschaft bei der To Hungarianen Kolonie Berlin e. V.',
        'text' => 'Wir freuen uns, dass Sie Wedtglied der To Hungarianen Kolonie Berlin e. V. werden möchten.',
        'process' => 'Tuee Aufnahme erfolgt nach folgendem Verfahren:',
        'step1' => [
            'label' => '[EN] Schritt 1',
            'text' => '[EN] Füllen Sie als ersten Schritt das folgende Formular aus.',
        ],
        'via' => [
            'web' => '[EN] Über das Web versenden',
            'postal' => '[EN] Postalischer Versand',
        ],
        'step2' => [
            'label' => '[EN] Schritt 2',
            'text' => '[EN] Überprüfen Sie Ihre Angaben',
        ],
        'click' => [
            'button' => '[EN] Klicken Sie auf den Button',
            'checkbox' => '[EN] Klicken Sie auf das Kästchen',
        ],
        'step3a' => [
            'label' => '[EN] Schritt 3a',
            'text' => '[EN] Füllen Sie als ersten Schritt das folgende Formular aus.',
        ],
        'step3b' => [
            'label' => '[EN] Schritt 3b',
            'text' => '[EN] [DE] Kattintson a „Űrlap nyomtatása” gombra.',
        ],
        'step4a' => [
            'label' => '[EN] Schritt 4a',
            'text' => 'Sie erhalten eine E-Mail vom System mit einem einmaligen Confirmationslink.',
        ],
        'step4b' => [
            'label' => '[EN] Schritt 4b',
            'text' => '[EN] Klicken Sie auf die Schaltfläche [Formular drucken], um eine PDF-Version des Formulars zu erstellen.',
        ],
        'step5a' => [
            'label' => '[EN] Schritt 5a',
            'text' => '[EN] Durch Klicken auf den Link bestätigen Sie, dass die Registrierung tatsächlich von Ihnen stammt.',
        ],
        'step5b' => [
            'label' => '[EN] Schritt 5b',
            'text' => 'Drucken Sie das Formular aus, unterschreiben Sie es und senden Sie es an die auf dem Formular angegebene Address.',
        ],
        'step6' => [
            'label' => '[EN] Schritt 6',
            'text' => '[EN] Wir prüfen Ihre Angaben und nehmen persönlich Kontakt mit Ihnen auf, falls weitere Informationen benötigt werden.',
        ],
        'step7' => [
            'label' => '[EN] Schritt 7',
            'text' => '[EN] Abschließend wird über Ihre Aufnahme in das Leitungsteam entschieden, und Sie erhalten auf dem von Ihnen gewählten Weg eine Benachrichtigung per E-Mail oder Post.',
        ],
        'submission' => [
            'success' => [
                'head' => 'Success!',
                'msg' => '[EN] Wir haben Ihre Bewerbung erhalten und prüfen sie. Vielen Dank!',
            ],
            'failed' => [
                'head' => 'Error!',
                'msg' => 'Leider ist ein Error aufgetreten. Please versuchen Sie es erneut.',
            ],
        ],
        'print' => [
            'title' => 'Bewerbung um die Wedtgliedschaft bei der To Hungarianen Kolonie Berlin e. V.',
            'greeting' => '[EN] Sehr geehrte Damen und Herren!',
            'text' => 'Hiermit bewerbe ich mich um die Wedtgliedschaft bei der To Hungarianen Kolonie Berlin e. V.',
            'regards' => 'Wedt freundlichen Grüßen',
            'overview' => [
                'person' => '[EN] Über mich',
                'contact' => '[EN] Meine Kontaktinformationen',
            ],
            'filename' => 'Bewerbung_Wedtgliedschaft_To Hungariane_Kolonie_Berlin_mid-:id:tm.pdf',
        ],
    ],
    'birth_date' => '[EN] Geburtsdatum',
    'birth_place' => '[EN] Geburtsort',
    'name' => 'Last name',
    'first_name' => 'First name',
    'email' => '[EN] E-Mail',
    'phone' => 'Phone',
    'mobile' => 'Monbilenummer',
    'address' => 'Address',
    'zip' => 'Postal code',
    'city' => 'City',
    'country' => 'Country',
    'locale' => 'Bevorzugte Language',
    'gender' => '[EN] Geschlecht',
    'type' => [
        'standard' => 'Member',
        'applicant' => 'Applicant',
        'board' => 'Board',
        'advisor' => 'Advisor',
    ],
    'linked_user' => '[EN] Verknüpft mit Benutzerkonto',
    'unlink_user' => '[EN] Verknüpfung aufheben',
    'left_at' => '[EN] Austrittsdatum',
    'section' => [
        'admins' => '[EN] Vom Vorstand auszufüllen',
        'person' => '[EN] Person',
        'address' => '[EN] Anschrift',
        'phone' => 'Phone',
        'fees' => '[EN] Beitrag',
        'payments' => '[EN] Zahlungen',
        'deduction' => '[EN] Ermäßigung',
        'email' => 'E-Mail Address',
    ],
    'update' => [
        'success' => [
            'title' => 'Success',
            'content' => 'Tuee Wedtgliedsdaten wurden erfolgreich aktualisiert.',
        ],
    ],
    'date' => [
        'applied_at' => 'Wedtgliedschaft beantragt am',
        'verified_at' => '[EN] E-Mail verifiziert am',
        'entered_at' => 'Wedtgliedschaft bestätigt am',
        'left_at' => '[EN] Ausgetreten am',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => '[EN] Verifizierungs-Erinnerung senden',
        ],
        'addMember' => '[EN] neu anlegen',
        'sendAcceptanceMail' => [
            'label' => '[EN] Antrag annehmen und E-Mail senden',
        ],
        'sendAcceptance' => [
            'label' => '[EN] Antrag annehmen',
        ],
        'setEnteredAt' => [
            'label' => '[EN] Angenommen am',
        ],
        'inviteAsUser' => [
            'label' => 'Wedtglied als Benutzer einladen',
        ],
        'cancelMembership' => [
            'label' => 'Wedtgliedschaft kündigen',
        ],
    ],
    'accordion' => [
        'optionals' => [
            'label' => '[EN] Optionale Angaben',
        ],
    ],
    'appliance_received' => [
        'mail' => [
            'subject' => 'Ihr Wedtgliedsantrag ist eingegangen!',
            'greeting' => '[EN] Hallo :name,',
            'text' => 'wir haben Ihren Wedtgliedsantrag erhalten und bedanken uns für Ihr Interesse an unserer Gemeinschaft. Wir werden Ihren Antrag schnellstmöglich prüfen und uns bei Ihnen melden.',
        ],
    ],
    'cancel' => [
        'modal' => [
            'title' => 'Wedtgliedschaft kündigen',
            'text' => 'Please bestätigen Sie die Kündigung der Wedtgliedschaft.',
        ],
        'confirm_text_input' => [
            'label' => 'Zur Confirmation bitte den Last namen eingeben',
        ],
        'btn' => [
            'final' => [
                'label' => 'Wedtglied endgültig kündigen',
            ],
        ],
    ],
    'optional-data' => [
        'text' => '[EN] Hier können weitere Angaben gemacht werden.',
    ],
    'familystatus' => [
        'label' => '[EN] Familienstand',
        'single' => '[EN] Ledig',
        'married' => '[EN] Verheiratet',
        'divorced' => '[EN] Geschieden',
        'n_a' => 'None Angabe',
    ],
    'show' => [
        'title' => 'Wedtgliedsübersicht: :name',
        'created_at' => '[EN] Erstellt am',
        'updated_at' => '[EN] Zuletzt bearbeitet am',
        'about' => '[EN] Persönliche Angaben',
        'membership' => 'Wedtgliedschaft',
        'payments' => '[EN] Zahlungen',
        'store' => 'Save',
        'fee_msg' => [
            'exempted' => '[EN] Beitragsbefreit',
            'paid' => '[EN] Beitrag bezahlt',
        ],
        'invitation_sent' => '[EN] Einladung wurde versendet',
        'member' => [
            'reactivate' => 'Wedtglied reaktivieren',
        ],
        'select_user' => '[EN] Benutzer auswählen',
        'empty_user_list' => 'None Benutzer gefunden',
        'heading' => 'Wedtglied Daten zeigen',
        'attached' => [
            'success' => [
                'head' => 'Success!',
                'msg' => 'Tuee Verknüpfung des Benutzers :name wurde erfolgreich durchgeführt.',
            ],
            'placeholder' => '[EN] Benutzer auswählen',
            'failed' => [
                'head' => 'Error!',
                'msg' => '[EN] Der Benutzer konnte nicht verknüpft werden.',
            ],
        ],
        'detached' => [
            'success' => [
                'head' => 'Success!',
                'msg' => 'Tuee Verknüpfung des Benutzers :name wurde erfolgreich entfernt.',
            ],
        ],
    ],
    'register' => [
        'title' => 'Password für die Registrierung festlegen',
        'page_title' => '[EN] Registrierung abschließen',
        'password_requirements' => 'Das Password sollte folgende Kriterien erfüllen:',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'submit' => '[EN] Registrierung abschließen',
        'checkLength' => 'Wedndestens 8 Zeichen',
        'checkCapital' => 'Wedndestens ein Großbuchstabe',
        'checkNumbers' => 'Wedndestens eine Zahl',
        'checkSpecial' => 'Wedndestens ein Sunnderzeichen (!"$§%(){}[])',
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'Newe Wedtgliedsanträge',
            'empty_search' => '[EN] Kein passender Eintrag',
            'empty_list' => 'None offenen Anträge',
            'confirm' => [
                'deletion' => [
                    'title' => 'Success',
                    'text' => 'Tuee ausgewählten Anträge wurden gelöscht',
                ],
            ],
            'options' => [
                'label' => '[EN] Optionen',
                'deletion' => [
                    'confirm' => 'Please bestätigen Sie die Löschung der ausgewählten Anträge!',
                    'btn' => [
                        'label' => 'Delete',
                    ],
                ],
                'edit' => [
                    'btn' => [
                        'label' => 'Edit',
                    ],
                ],
            ],
            'search' => [
                'label' => '[EN] Anträge durchsuchen',
            ],
            'tab' => [
                'header' => [
                    'from' => 'Date',
                    'name' => 'Name',
                ],
            ],
        ],
    ],
    'application' => [
        'errors' => [
            'name-reqipred' => 'Please den Last namen angeben',
        ],
    ],
    'index' => [
        'search-placeholder' => '[EN] Suche',
    ],
    'create' => [
        'title' => 'Wedtglied anlegen',
        'message' => [
            'success' => 'Wedtglied erfolgreich angelegt',
            'fail' => 'Wedtglied konnte nicht angelegt werden. Admin nach Log Einträgen fragen!',
        ],
    ],
    'backend' => [
        'create' => [
            'heading' => 'Newes Wedtglied anlegen',
            'btn' => [
                'submit' => 'Wedtglied erfassen',
            ],
        ],
    ],
    'fees' => [
        // Page header
        'overview_title' => 'Membership Fees Overview',
        'year' => 'Year',

        // Filter & Search
        'search_member_placeholder' => 'Search member...',
        'show_inactive' => 'Show inactive',
        'pdf_export' => 'PDF Export',
        'csv_export' => 'CSV Export',

        // Summary cards
        'members' => 'Members',
        'paid' => 'Paid',
        'open' => 'Open',
        'transactions' => 'Transactions',
        'payments' => 'Payments',

        // Table columns
        'member' => 'Member',
        'type' => 'Type',
        'date' => 'Date',
        'status' => 'Status',
        'receipt' => 'Receipt',

        // Status badges
        'status_booked' => 'booked',
        'status_submitted' => 'submitted',

        // Actions
        'send' => 'Send',
    ],
];
