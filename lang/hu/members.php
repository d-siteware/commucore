<?php

declare(strict_types=1);

return [
    // Seitenüberschriften & allgemeine Texte
    'title' => 'Tagjaink áttekintése',
    'header' => 'Itt találja az összes tagunk rendezhető áttekintését. Az almenüben szerkesztheti a tagokat, rögzítheti a befizetéseket, vagy inaktívként megjelölheti őket. Ez utóbbi helyettesíti a végleges törlést.',

    // Tabellenüberschriften
    'table.header.name' => 'Név',
    'table.header.phone' => 'Mobiltelefon',
    'table.header.status' => 'Státusz',
    'table.header.fee_status' => 'Tagdíj státusz',
    'table.header.birthday' => 'Születésnap',

    // Kontextmenü (3-Punkte-Menü)
    'con.men.edit' => 'Szerkesztés',
    'con.men.payment' => 'Befizetés rögzítése',
    'con.men.delete' => 'Inaktívként megjelölés',

    // Geburtstags-Widget
    'widget.birthday.card.table.header.member' => 'Tag',
    'widget.birthday.card.table.header.birthday' => 'Születésnap',
    'widget.birthday.card.table.header.newage' => 'Életkor',
    'widget.birthday.card.heading' => 'Közelgő születésnapok: :name',

    // Tagdíj-Typen
    'fee-type.free' => 'Tagdíjmentesség',
    'fee-type.standard' => 'Normál tagdíj',
    'fee-type.discounted' => 'Kedvezményes tagdíj',
    'fee_type' => 'Tagdíj típusa',

    // Bewerbungsformular – Tagdíj-Infos
    'apply.discount.label' => 'Kedvezményes tagdíj igénylése',
    'apply.fee.text' => 'Tájékoztatást kaptam a havi :sum € tagdíjról, és kötelezem magam annak megfizetésére.',
    'apply.discount.reason.label' => 'A kedvezmény indoka',
    'apply.full_fee.label' => 'A fizető tagok havi :sum € tagdíjat fizetnek.',
    'apply.discounted_fee.label' => 'A tagok kedvezményes havi :sum € tagdíjat igényelhetnek.',
    'apply.free_fee.label' => ':age év feletti tagjaink mentesülnek a tagdíjfizetési kötelezettség alól.',
    'apply.fee.label' => 'A fizető tagok havi :sum € tagdíjat fizetnek. A 75 év feletti tagok mentesülnek a tagdíjfizetés alól.',
    'apply.fee.payment.banktt' => 'A tagdíjat a megadott bankszámlaszámra kérjük utalni.',
    'apply.fee.payment.paypals' => 'A tagdíj PayPal-on is küldhető. Kérjük, a „Barátoknak küldött pénz” opciót válassza, különben a PayPal 1,8%-os díjat von le.',
    'apply.fee.payment.paypal' => 'A tagdíj a :iban PayPal-címre küldhető. Kérjük, a „Barátoknak küldött pénz” opciót válassza, különben a PayPal díjat von le.',

    // E-Mail-Adresse & Druckoption
    'apply.email.none' => 'Nincs e-mail címem!',
    'apply.email.without.text' => 'Ha nem rendelkezik e-mail-címmel, nyomtassa ki ezt az űrlapot, írja alá, és küldje el postai úton az alábbi címre:',
    'apply.email.benefits' => 'Az e-mail-címmel rendelkező tagok automatikusan értesítést kapnak a rendezvényekről, és hozzáférnek a hirdetőtáblához.',

    // Formular-Buttons
    'apply.checkAndSubmit' => 'Adatok ellenőrzése és elküldése',
    'apply.printAndSubmit' => 'Űrlap nyomtatása',

    // Formularfelder
    'birth_date' => 'Születési dátum',
    'birth_place' => 'Születési hely',
    'name' => 'Vezetéknév',
    'first_name' => 'Keresztnév',
    'email' => 'E-mail-cím',
    'phone' => 'Telefon',
    'mobile' => 'Mobiltelefon',
    'address' => 'Lakcím',
    'zip' => 'Irányítószám',
    'city' => 'Város',
    'country' => 'Ország',
    'locale' => 'Előnyben részesített nyelv',
    'gender' => 'Nem',
    'type' => 'Tagsági státusz',
    'linked_user' => 'Felhasználói fiókhoz csatolva',
    'unlink_user' => 'Kapcsolat megszüntetése',
    'left_at' => 'Kilépés dátuma',

    // Admin-Sektion
    'section.admins' => 'Csak a vezetőség töltheti ki',

    // Erfolgsmeldungen
    'update.success.title' => 'Sikeres mentés',
    'update.success.content' => 'A tag adatai sikeresen frissítve lettek.',

    // Dátumok
    'date.applied_at' => 'Tagság kérelmezve',
    'date.verified_at' => 'E-mail megerősítve',
    'date.entered_at' => 'Tagság elfogadva',
    'date.left_at' => 'Kilépés dátuma',

    // Admin-Buttons
    'btn.sendVerificationMail.label' => 'Emlékeztető e-mail küldése',
    'btn.addMember' => 'Új tag felvétele',
    'btn.sendAcceptanceMail.label' => 'Kérelem elfogadása és e-mail küldése',
    'btn.sendAcceptance.label' => 'Kérelem elfogadása',
    'btn.setEnteredAt.label' => 'Elfogadozás dátuma',
    'btn.inviteAsUser.label' => 'Tag meghívása felhasználóként',
    'btn.cancelMembership.label' => 'Tagság megszüntetése',

    // Accordion & optionale Angaben
    'accordion.optionals.label' => 'Opcionális adatok',
    'optional-data.text' => 'Itt további információkat adhat meg.',

    // Familienstand
    'familystatus.label' => 'Családi állapot',
    'familystatus.single' => 'Hajadon / Nőtlen',
    'familystatus.married' => 'Házas',
    'familystatus.divorced' => 'Elvált',
    'familystatus.n_a' => 'Nem kívánom megadni',

    // Mitgliedstypen
    'type.standard' => 'Tag',
    'type.applicant' => 'Jelölt',
    'type.board' => 'Vezetőségi tag',
    'type.advisor' => 'Tanácsadó',

    // Detailseite (show)
    'show.title' => 'Tag adatai: :name',
    'show.created_at' => 'Létrehozva',
    'show.updated_at' => 'Utoljára módosítva',
    'show.about' => 'Személyes adatok',
    'show.membership' => 'Tagság',
    'show.payments' => 'Befizetések',
    'show.store' => 'Mentés',
    'show.fee_msg.exempted' => 'Tagdíjmentes',
    'show.fee_msg.paid' => 'Tagdíj rendezve',
    'show.invitation_sent' => 'Meghívó elküldve',
    'show.member.reactivate' => 'Tag újraaktiválása',
    'show.select_user' => 'Felhasználó kiválasztása',
    'show.empty_user_list' => 'Nincs találat',

    // Benutzer verknüpfen
    'show.attached.success.head' => 'Siker!',
    'show.attached.success.msg' => ':name felhasználó sikeresen csatolva lett.',
    'show.attached.failed.head' => 'Hiba!',
    'show.attached.failed.msg' => 'A felhasználó nem csatolható.',
    'show.detached.success.head' => 'Siker!',
    'show.detached.success.msg' => ':name felhasználó csatolása megszüntetve.',

    // Registrierung / Einladung
    'register.title' => 'Jelszó beállítása a regisztrációhoz',
    'register.page_title' => 'Regisztráció befejezése',
    'register.password_requirements' => 'A jelszónak meg kell felelnie az alábbi követelményeknek:',
    'register.password' => 'Jelszó',
    'register.password_confirm' => 'Jelszó megerősítése',
    'register.submit' => 'Regisztráció befejezése',
    'register.checkLength' => 'Legalább 8 karakter',
    'register.checkCapital' => 'Legalább egy nagybetű',
    'register.checkNumbers' => 'Legalább egy szám',
    'register.checkSpecial' => 'Legalább egy speciális karakter (!"$§%(){}[])',

    // Sektionen im Formular
    'section.person' => 'Személyes adatok',
    'section.address' => 'Lakcím',
    'section.phone' => 'Elérhetőségek',
    'section.fees' => 'Tagdíj',
    'section.payments' => 'Befizetések',
    'section.deduction' => 'Kedvezmény',
    'section.email' => 'E-mail-cím',

    // Widgets
    'widgets.applicants.title' => 'Új tagsági kérelmek',
    'widgets.applicants.empty_search' => 'Nincs találat',
    'widgets.applicants.empty_list' => 'Nincs függőben lévő kérelem',
    'widgets.applicants.confirm.deletion.title' => 'Sikeres törlés',
    'widgets.applicants.confirm.deletion.text' => 'A kijelölt kérelmek törölve lettek.',
    'widgets.applicants.options.label' => 'Műveletek',
    'widgets.applicants.options.deletion.confirm' => 'Kérjük, erősítse meg a kijelölt kérelmek törlését!',
    'widgets.applicants.options.edit.btn.label' => 'Szerkesztés',
    'widgets.applicants.options.deletion.btn.label' => 'Törlés',
    'widgets.applicants.tab.header.from' => 'Dátum',
    'widgets.applicants.tab.header.name' => 'Név',

    // Bewerbungsseite (extern)
    'apply.title' => 'Tagsági kérelem – Ungarische Kolonie Berlin e.V.',
    'apply.text' => 'Örülünk, hogy érdeklődik a tagság iránt az Ungarische Kolonie Berlin e.V.-nél!',
    'apply.process' => 'A felvétel az alábbi lépésekben történik:',
    'apply.step1.label' => '1. lépés',
    'apply.step1.text' => 'Töltse ki az alábbi űrlapot.',
    'apply.via.web' => 'Online elküldés',
    'apply.via.postal' => 'Postai úton',
    'apply.email.note.header' => 'Fontos!',
    'apply.email.note.content' => 'Az online beküldéshez kötelező e-mail-címet megadni. Ha nincs e-mail-címe, válassza a postai útvonalat.',
    'apply.step2.label' => '2. lépés',
    'apply.step2.text' => 'Ellenőrizze az adatokat',
    'apply.click.button' => 'Kattintson a gombra',
    'apply.click.checkbox' => 'Jelölje be a négyzetet',
    'apply.step3a.label' => '3a. lépés (online)',
    'apply.step3a.text' => 'Kattintson az „Adatok ellenőrzése és elküldése” gombra.',
    'apply.step3b.label' => '3b. lépés (postai)',
    'apply.step3b.text' => 'Kattintson a „Űrlap nyomtatása” gombra.',
    'apply.step4a.label' => '4a. lépés (online)',
    'apply.step4a.text' => 'A rendszer egy egyszeri megerősítő linket küld e-mail-ben.',
    'apply.step4b.label' => '4b. lépés (postai)',
    'apply.step4b.text' => 'Nyomtassa ki és írja alá az űrlapot, majd küldje el a rajta szereplő címre.',
    'apply.step5a.label' => '5a. lépés (online)',
    'apply.step5a.text' => 'A linkre kattintva igazolja, hogy Ön küldte a kérelmet.',
    'apply.step5b.label' => '5b. lépés (postai)',
    'apply.step5b.text' => 'A aláírt űrlapot postázza el.',
    'apply.step6.label' => '6. lépés',
    'apply.step6.text' => 'Kérelem feldolgozása alatt áll. Szükség esetén felvesszük Önnel a kapcsolatot.',
    'apply.step7.label' => '7. lépés',
    'apply.step7.text' => 'A vezetőség dönt a felvételről, és értesítjük Önt e-mailben vagy postai úton.',

    // Erfolgs-/Fehlermeldungen bei Absenden
    'apply.submission.success.head' => 'Sikeres beküldés!',
    'apply.submission.success.msg' => 'Kérelmét megkaptuk, hamarosan feldolgozzuk. Köszönjük szépen!',
    'apply.submission.failed.head' => 'Hiba történt',
    'apply.submission.failed.msg' => 'Sajnos technikai hiba lépett fel. Kérjük, próbálja újra később.',

    // PDF-Druckversion
    'apply.print.title' => 'Tagsági kérelem – Ungarische Kolonie Berlin e.V.',
    'apply.print.greeting' => 'Tisztelt Elnökség!',
    'apply.print.text' => 'Ezennel kérem felvételemet az Ungarische Kolonie Berlin e.V. tagjai közé.',
    'apply.print.regards' => 'Tisztelettel',
    'apply.print.overview.person' => 'Személyes adataim',
    'apply.print.overview.contact' => 'Elérhetőségeim',
    'apply.print.filename' => 'Tagsagi_kerelem_Ungarische_Kolonie_Berlin_:id.pdf',

    // Sonstiges
    'cancel.modal.title' => 'Tagság megszüntetése',
    'cancel.modal.text' => 'Kérjük, erősítse meg a tagság megszüntetését.',
    'cancel.confirm_text_input.label' => 'Erősítse meg a vezetéknév megadásával',
    'cancel.btn.final.label' => 'Tagság végleges megszüntetése',

    'appliance_received.mail.subject' => 'Tagsági kérelme megérkezett!',
    'appliance_received.mail.greeting' => 'Kedves :name!',
    'appliance_received.mail.text' => 'Megkaptuk tagsági kérelmét, köszönjük érdeklődését közösségünk iránt. Hamarosan feldolgozzuk és visszajelzünk Önnek.',

    'create.message.success' => 'Az új tag sikeresen létrehozva.',
    'create.message.fail' => 'A tag létrehozása nem sikerült. Kérjük, jelezze az adminisztrátornak.',
    'index' => [
        'search-placeholder' => 'Keresés',
    ],
    'backend.create.heading' => 'Új tag felvétele',
    'backend.create.btn.submit' => 'Tag rögzítése',

];
