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
            'delete' => 'Kündigen',
            'reactivate' => 'Aktivieren',
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
        'label' => 'Beitragsstatus',
        'free' => 'Beitragsbefreit',
        'standard' => 'Standardbeitrag',
        'discounted' => 'Ermäßigter Beitrag',
    ],
    'apply' => [
        'dsgvo' => [
            'section' => [
                'label' => 'Einwilligungen',
                'text' => 'Damit wir den datenschutzkonformen Umgang mit Ihren Daten gewährleisten können, bitten wir Sie um die folgenden Einwilligungen. Sie können diese jederzeit widerrufen. Nähere Hinweise finden Sie in unserer Datenschutzerklärung.',
            ],
            'gdpr' => [
                'label' => 'Datenschutz',
                'description' => 'Ich stimme zu, dass meine im Antrag angegebenen personenbezogenen Daten zum Zweck der Bearbeitung meines Mitgliedsantrags sowie zur Verwaltung meiner Mitgliedschaft gespeichert und verarbeitet werden.',
                'required' => 'Diese Einwilligung ist erforderlich damit die Anmeldung erfolgen kann.',
            ],
            'newsletter' => [
                'label' => 'Benachrichtigungen',
                'description' => 'Ich bin damit einverstanden, per E-Mail über Veranstaltungen, Vereinsaktivitäten und wichtige Informationen des Vereins informiert zu werden.',
            ],
            'photo' => [
                'label' => 'Foto/Video',
                'description' => 'Ich erkläre mich damit einverstanden, dass im Rahmen von Vereinsveranstaltungen aufgenommene Fotos oder Videos, auf denen ich möglicherweise zu sehen bin, für Vereinszwecke (z. B. Website, Newsletter oder Vereinsdokumentation) verwendet werden dürfen.',
            ],
        ],
        'expired' => ['title' => 'Abgelaufen', 'text' => 'Der Link zur Bestätigung der E-Mail Adresse ist abgelaufen. Bitte versuchen Sie es erneut oder setzen sich mit uns in Verbindung.'],
        'invalid' => ['title' => 'Ungültig', 'text' => 'Diese Link ist nicht gültig oder existiert nicht mehr.'],
        'verify' => [
            'title' => 'E-Mail Adresse bestätigen',
            'greeting' => 'Hallo :name!',
            'summary' => 'Wir haben folgende Daten erfasst. Bitte bestätigen Sie Ihre E-Mail-Adresse, um fortzufahren.',
            'submit' => 'Bestätigung mit Datenschutzeinwilligungen speichern',
            'mail' => [
                'subject' => 'Ihr Antrag auf Mitgliedschaft bei der :organization wurde erfasst!',
                'greeting' => 'Guten Tag :name,',
                'line1' => 'Wir haben Ihren Antrag auf Mitgliedschaft erhalten. Bitte bestätigen Sie Ihre E-Mail-Adresse, um fortzufahren.',
                'action' => 'E-Mail Adresse bestätigen',
                'expires' => 'Der Link ist 48 Stunden gültig',
                'line2' => 'Mit der Bestätigung der E-Mail Adresse wird Ihr Antrag auf Mitgliedschaft bei der :organization eingereicht.',
            ],
        ],
        'pending' => [
            'title' => 'Antrag auf Mitgliedschaft',
            'text' => 'Vielen Dank für Ihren Antrag. Sie werden in Kürze eine E-Mail von uns erhalten, damit Sie die angegebene E-Mail Adresse bestätigen können.',
        ],
        'validation' => [
            'email' => [
                'application_pending' => 'Mit dieser E-Mail-Adresse wurde bereits ein Antrag auf Mitgliedschaft eingereicht.',
                'already_member' => 'Diese E-Mail-Adresse ist bereits als Mitglied registriert.',

            ],
        ],
        'done' => [
            'title' => 'Geschafft 🎉',
            'text' => 'Ihr Antrag wurde erfolgreich eingereicht. Vielen Dank! Wir werden uns bei Ihnen melden.',
        ],
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
        'title' => 'Antrag auf Mitgliedschaft bei der :name',
        'text' => 'Wir freuen uns, dass Sie Mitglied der :name werden möchten.',
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
            'title' => 'Bewerbung um die Mitgliedschaft bei der :name',
            'greeting' => 'Sehr geehrte Damen und Herren!',
            'text' => 'Hiermit bewerbe ich mich um die Mitgliedschaft bei der :name',
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
    'deduction_reason' => 'Älter als :age Jahre',
    'type' => [
        'label' => 'Mitgliedschaftstyp',
        'exempt' => 'Ausgeschlossen',
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
        'gdpr_consent_at' => 'Datenschutz bestätigt am',
        'newsletter_consent_at' => 'Newsletter bestätigt am',
        'photo_consent_at' => 'Foto/Video bestätigt am',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => 'Verifizierungs-Erinnerung senden',
        ],
        'addMember' => 'Neu anlegen',
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
        'change_requests' => 'Änderungsanträge',
        'payments' => 'Zahlungen',
        'store' => 'Speichern',
        'payments_made' => 'Getätigte Zahlungen',
        'new_payment' => 'Neue Zahlung erfassen',
        'payment_label' => 'Text',
        'amount' => 'Betrag',
        'receipts' => 'Belege',
        'delete_user' => 'Nutzer löschen!',
        'documents' => 'Dokumente',
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
    'notifications' => [
        'new_applicant' => [
            'intro' => 'Neuer Antrag',
            'subject' => 'Neuer Antrag',
            'text' => 'Ein neuer Antrag wurde eingegangen.',
            'cta' => 'Im Dashboard ansehen',
            'reply_subject' => 'Ihre Antrag auf Mitgliedschaft in der :name',
        ],
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'Neue Mitgliedsanträge',
            'empty_search' => 'Kein passender Eintrag',
            'empty_list' => 'Keine offenen Anträge',
            'modal' => [
                'title' => 'Antrag anzeigen',
                'reject' => [
                    'title' => 'Absage',
                    'subtitle' => 'Die Absage muss begründet werden',
                    'reason_label' => 'Begründung',
                    'reason_placeholder' => 'Leider können wir Ihre Bewerbung ...',
                    'confirm_btn' => 'Absage versenden',
                ],
                'fields' => [
                    'applied_at' => 'Beworben am :date',
                    'email' => 'E-Mail',
                    'birth_date' => 'Geburtstag',
                    'phone' => 'Telefon',
                    'address' => 'Anschrift',
                    'gdpr' => 'Datenschutz',
                    'newsletter' => 'Newsletter',
                    'photo_consent' => 'Foto/Video',

                ],
                'btn' => [
                    'cancel' => 'Abbruch',
                    'reject' => 'Absagen',
                    'accept' => 'Annehmen',
                ],
            ],
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
            'name-required' => 'Bitte den Nachnamen angeben',
        ],
    ],
    'index' => [
        'search-placeholder' => 'Suche',
        'filter_by_status' => 'Nach Status filtern',
    ],
    'create' => [
        'title' => 'Mitglied anlegen',
        'account_label' => 'Konto: :name',
        'message' => [
            'success' => 'Mitglied erfolgreich angelegt',
            'fail' => 'Mitglied konnte nicht angelegt werden. Admin nach Log Einträgen fragen!',
        ],
    ],
    'backend' => [
        'cancel' => [
            'success' => [
                'head' => 'Mitgliedschaft gekündigt',
                'msg' => 'Die Mitgliedschaft wurde erfolgreich gekündigt.',
            ],
            'forbidden' => [
                'head' => 'Keine Berechtigung',
                'msg' => 'Du bist nicht berechtigt, diese Mitgliedschaft zu kündigen. (:error)',
            ],
            'modal' => [
                'title' => 'Mitgliedschaft kündigen',
                'subtitle' => 'Mitgliedschaft von :name kündigen. Diese Aktion kann nicht rückgängig gemacht werden.',
                'date_label' => 'Austrittsdatum',
                'confirm' => 'Jetzt kündigen',
            ],
        ],

        'pseudonymize' => [
            'success' => [
                'head' => 'Mitglied pseudonymisiert',
                'msg' => 'Die Daten des Mitglieds wurden erfolgreich pseudonymisiert.',
            ],
            'forbidden' => [
                'head' => 'Keine Berechtigung',
                'msg' => 'Du bist nicht berechtigt, dieses Mitglied zu pseudonymisieren. (:error)',
            ],
            'modal' => [
                'title' => 'Mitglied pseudonymisieren',
                'subtitle' => 'Alle personenbezogenen Daten von :name werden unwiderruflich gelöscht.',
                'confirm' => 'Jetzt pseudonymisieren',
            ],
            'scheduled' => [
                'head' => 'Automatische Pseudonymisierung',
                'msg' => ':count Mitglied(er) wurden pseudonymisiert.',
            ],
        ],
        'create' => [
            'heading' => 'Neues Mitglied anlegen',
            'btn' => [
                'submit' => 'Mitglied erfassen',
            ],
        ],
        'form' => [
            'no-user-found' => 'Kein Benutzer gefunden',
        ],
        'attach' => [
            'failed' => [
                'head' => 'Fehler',
                'msg' => 'Benutzer konnte nicht zugeordnet werden.',
            ],
        ],
        'invitation' => [
            'sent' => [
                'head' => 'Erfolg',
                'msg' => 'Einladung wurde verschickt.',
            ],
            'failed' => [
                'head' => 'Fehler',
                'msg' => 'Einladung wurde nicht verschickt: :error',
            ],
        ],
        'application' => [
            'accepted' => [
                'head' => 'Erfolg',
                'msg' => 'Mitgliedschaft wurde angenommen.',
            ],
        ],
        'delete' => [
            'success' => [
                'head' => 'Erfolg',
                'msg' => 'Mitgliedschaft wurde gekündigt.',
            ],
            'user_deleted' => [
                'msg' => 'Benutzer wurde gelöscht.',
            ],
            'user_failed' => [
                'msg' => 'Fehler beim Löschen des Benutzers :id.',
            ],
        ],

        'reactivate' => [
            'success' => [
                'head' => 'Erfolg',
                'msg' => 'Mitgliedschaft wurde wiederhergestellt.',
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
    'documents' => [

        'btn' => [
            'upload' => 'Dokument hochladen',
            'save' => 'Speichern',
            'download' => 'Herunterladen',
            'cancel' => 'Abbrechen',
        ],

        'upload' => [
            'title' => 'Neues Dokument hochladen',
            'file_label' => 'Datei (PDF, JPG, PNG, TIF)',
            'notes_label' => 'Notiz (optional)',
        ],

        'category' => [
            'label' => 'Kategorie',
            'placeholder' => 'Kategorie wählen…',
            'membership_form' => 'Mitgliedschaftsantrag',
            'sepa' => 'SEPA-Lastschriftmandat',
            'privacy' => 'Datenschutzerklärung',
            'id_document' => 'Ausweisdokument',
            'other' => 'Sonstiges',
        ],

        'table' => [
            'name' => 'Dateiname',
            'category' => 'Kategorie',
            'size' => 'Größe',
            'uploaded_by' => 'Hochgeladen von',
            'last_accessed' => 'Zuletzt geöffnet',
            'actions' => 'Aktionen',
        ],

        'confirm' => [
            'delete' => 'Dokument wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
        ],

        'upload_success' => 'Das Dokument wurde erfolgreich hochgeladen.',
        'delete_success' => 'Das Dokument wurde gelöscht.',
        'empty' => 'Für dieses Mitglied sind noch keine Dokumente hinterlegt.',

        'errors' => [
            'unauthorized' => 'Du hast keine Berechtigung für diese Aktion.',
            'upload_failed' => 'Beim Hochladen ist ein Fehler aufgetreten. Bitte versuche es erneut.',
            'file_not_found' => 'Die Datei wurde im Speicher nicht gefunden.',
            'invalid_file_type' => 'Nur PDF, JPG, PNG und TIF/TIFF sind erlaubt.',
            'file_too_large' => 'Die Datei darf maximal 10 MB groß sein.',
            'mime_not_allowed_for_category' => 'Dieser Dateityp ist für die gewählte Kategorie nicht erlaubt.',
        ],

    ],
    'export' => [
        'title' => 'Mitglieder exportieren',
        'description' => 'Wähle den Export-Typ und die gewünschten Filter. Der Download startet nach dem Klick auf den Button.',
        'type_label' => 'Export-Typ',
        'filter_label' => 'Filter',
        'preview_count' => 'Mitglieder entsprechen den Filterkriterien',
        'btn_download' => 'Herunterladen',
        'btn_download_empty' => 'Keine Mitglieder gefunden',
        'btn_label' => 'Export',
        'type' => [
            'stammdaten' => 'Stammdaten',
            'stammdaten_desc' => 'Name, Adresse, Kontaktdaten',
            'members_all' => 'Alle Mitgliedsdaten',
            'members_all_desc' => 'Alle Felder inkl. Rollen, Beitragstyp und Mitgliedschaftsstatus',
            'full' => 'Vollexport (ZIP)',
            'full_desc' => 'Alle Daten + angehängte Dokumente als ZIP-Archiv',
        ],

        'filter' => [
            'only_active' => 'Nur aktive Mitglieder (kein Austrittsdatum)',
            'include_pseudonymized' => 'Pseudonymisierte Mitglieder einschließen',
            'member_types' => 'Mitgliedertypen',
        ],
    ],
    'import' => [
        'btn_label' => 'Import',
        'page_title' => 'Mitglieder importieren',
        'mail' => [
            'subject' => 'Mitglieder-Import abgeschlossen',
            'heading' => 'Import abgeschlossen',
            'greeting' => 'Hallo :name,',
            'intro' => 'Der Mitglieder-Import vom :date wurde erfolgreich abgeschlossen.',
            'imported' => 'Importiert',
            'skipped' => 'Übersprungen (Duplikate)',
            'errors' => 'Fehler',
            'duration' => 'Dauer',
            'error_details' => 'Fehlerdetails',
            'error_row' => 'Zeile :row',
            'backup_info' => 'Vor dem Import wurde ein Backup der Mitgliederdaten erstellt.',
            'backup_download' => 'Backup herunterladen',
            'backup_expiry' => 'Der Download-Link ist 24 Stunden gültig.',
            'footer' => 'Bei Fragen wende dich an den Administrator.',
            'failed_subject' => 'Mitglieder-Import fehlgeschlagen',
            'failed_heading' => 'Import fehlgeschlagen',
            'failed_greeting' => 'Hallo :name,',
            'failed_intro' => 'Der Mitglieder-Import konnte leider nicht abgeschlossen werden.',
            'failed_footer' => 'Bitte prüfe die ZIP-Datei und versuche es erneut.',

        ],
        'title' => 'Mitglieder importieren',
        'description' => 'Importiere Mitgliederdaten aus einer CSV- oder ZIP-Datei.',
        'btn_back' => 'Zurück',
        'btn_cancel' => 'Abbrechen',

        'upload' => [
            'title' => 'Datei hochladen',
            'description' => 'Wähle den Import-Typ und lade die entsprechende Datei hoch.',
            'type_label' => 'Import-Typ',
            'file_label_csv' => 'CSV-Datei auswählen',
            'file_label_zip' => 'ZIP-Datei auswählen',
            'zip_hint' => 'ZIP-Dateien werden auf Echtheit geprüft (Checksum). Nur Exporte aus CommuCore werden akzeptiert.',
            'error_heading' => 'Fehler beim Einlesen',
            'btn_upload' => 'Datei einlesen',
            'btn_uploading' => 'Wird eingelesen…',
            'dropzone_heading_csv' => 'CSV-Datei hier ablegen oder klicken',
            'dropzone_heading_zip' => 'ZIP-Datei hier ablegen oder klicken',
            'remove_file' => 'Datei entfernen',
            'zip_async_hint' => 'ZIP-Importe werden im Hintergrund verarbeitet. Du erhältst eine E-Mail wenn der Import abgeschlossen ist.',
            'zip_job_dispatched' => 'Import gestartet',
            'zip_job_description' => 'Die ZIP-Datei wird im Hintergrund verarbeitet. Du erhältst eine E-Mail sobald der Import abgeschlossen ist.',
            'template_hint' => 'Noch keine Datei? Lade eine leere Vorlage herunter:',
            'template_download' => 'CSV-Vorlage herunterladen',
        ],

        'mapping' => [
            'title' => 'Felder zuordnen',
            'description' => 'Ordne die Spalten der CSV-Datei den CommuCore-Feldern zu.',
            'col_csv' => 'CSV-Spalte',
            'col_commucore' => 'CommuCore-Feld',
            'fields_mapped' => 'Felder zugeordnet',
            'btn_confirm' => 'Zuordnung bestätigen',
            'enum_modal_title' => 'Unbekannte Werte zuordnen',
            'enum_modal_description' => 'Folgende Werte konnten nicht automatisch zugeordnet werden. Bitte ordne sie manuell zu oder wähle "Ignorieren".',
            'enum_skip' => 'Ignorieren',
            'enum_modal_confirm' => 'Zuordnung übernehmen',
        ],

        'preview' => [
            'title' => 'Vorschau & Backup',
            'description' => ':total Zeilen gefunden, :duplicates Duplikate erkannt.',
            'total_rows' => 'Zeilen gesamt',
            'new_rows' => 'Neu',
            'duplicate_rows' => 'Duplikate',
            'duplicate' => 'Duplikat',
            'new' => 'Neu',
            'more_rows' => '… und :count weitere Zeilen',
            'backup_required' => 'Backup erforderlich',
            'backup_description' => 'Vor dem Import wird automatisch ein Backup der aktuellen Mitgliederdaten erstellt.',
            'backup_created' => 'Backup erstellt',
            'backup_download' => 'Backup herunterladen',
            'btn_backup' => 'Backup erstellen & weiter',
            'btn_backup_loading' => 'Backup wird erstellt…',
            'btn_continue' => 'Import starten',
        ],

        'log' => [
            'skipped' => [
                'label' => 'Übersprungen',
                'duplicate' => 'Duplikat',
                'error' => 'Fehler',
            ],
            'completed' => [
                'label' => 'Import abgeschlossen',
            ],
        ],

        'import' => [
            'title' => 'Import durchführen',
            'description' => ':count Mitglieder werden importiert.',
            'warning_heading' => 'Achtung',
            'warning_text' => 'Der Import kann nicht automatisch rückgängig gemacht werden. Ein Rollback ist nur über das erstellte Backup möglich.',
            'confirm' => 'Import wirklich starten?',
            'btn_start' => ':count Mitglieder importieren',
            'in_progress' => 'Import läuft…',
            'success_heading' => 'Import erfolgreich abgeschlossen',
            'btn_finish' => 'Abschließen',
            'rollback_confirm' => 'Rollback wirklich durchführen? Alle importierten Daten werden gelöscht.',
            'btn_rollback' => 'Rollback durchführen',
            'btn_rolling_back' => 'Rollback läuft…',
        ],
    ],
    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Ausgetreten',
    ],
];
