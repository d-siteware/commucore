<?php

declare(strict_types=1);

return [
    'title' => 'Tagok áttekintése',
    'header' => 'Itt találja az összes tag rendezhető áttekintését. Az almenüben a tagok szerkeszthetők, befizetések rögzíthetők vagy a tagok inaktívvá jelölhetők. Utóbbi helyettesíti a bejegyzés törlését.',
    'table' => [
        'header' => [
            'name' => 'Név',
            'phone' => 'Mobil szám',
            'status' => 'Státusz',
            'fee_status' => 'Tagdíj státusz',
            'birthday' => 'Születésnap',
        ],
    ],
    'con' => [
        'men' => [
            'edit' => 'Szerkesztés',
            'payment' => 'Befizetés rögzítése',
            'delete' => 'Lemondás',
            'reactivate' => 'Aktiválás',
        ],
    ],
    'widget' => [
        'birthday' => [
            'card' => [
                'table' => [
                    'header' => [
                        'member' => 'Tag',
                        'birthday' => 'Születési dátum',
                        'newage' => 'Életkor',
                    ],
                ],
                'heading' => 'Közelgő születésnapok itt: :name',
            ],
        ],
    ],
    'fee-type' => [
        'label' => 'Tagdíj státusz',
        'free' => 'Tagdíjmentes',
        'standard' => 'Normál tagdíj',
        'discounted' => 'Kedvezményes tagdíj',
    ],
    'apply' => [
        'dsgvo' => [
            'section' => [
                'label' => 'Hozzájárulások',
                'text' => 'Annak érdekében, hogy biztosíthassuk adatai adatvédelmi szabályoknak megfelelő kezelését, kérjük az alábbi hozzájárulásokat. Ezeket bármikor visszavonhatja. További információkat adatvédelmi nyilatkozatunkban talál.',
            ],
            'gdpr' => [
                'label' => 'Adatvédelem',
                'description' => 'Hozzájárulok, hogy a kérelemben megadott személyes adataimat a tagsági kérelem feldolgozása, valamint tagságom kezelése céljából tárolják és feldolgozzák.',
                'required' => 'Ez a hozzájárulás szükséges a regisztráció végrehajtásához.',
            ],
            'newsletter' => [
                'label' => 'Értesítések',
                'description' => 'Hozzájárulok, hogy e-mailben tájékoztassanak az egyesület rendezvényeiről, tevékenységeiről és fontos információiról.',
            ],
            'photo' => [
                'label' => 'Fotó/Videó',
                'description' => 'Hozzájárulok, hogy az egyesületi rendezvényeken készült fényképeket vagy videókat, amelyeken esetleg látható vagyok, egyesületi célokra (pl. weboldal, hírlevél vagy egyesületi dokumentáció) felhasználják.',
            ],
        ],
        'expired' => ['title' => 'Lejárt', 'text' => 'Az e-mail cím megerősítéséhez vezető link lejárt. Kérjük, próbálja újra, vagy vegye fel velünk a kapcsolatot.'],
        'invalid' => ['title' => 'Érvénytelen', 'text' => 'Ez a link nem érvényes, vagy már nem létezik.'],
        'verify' => [
            'title' => 'E-mail cím megerősítése',
            'greeting' => 'Szia :name!',
            'summary' => 'Az alábbi adatokat rögzítettük. Kérjük, erősítse meg e-mail címét a folytatáshoz.',
            'submit' => 'Megerősítés mentése adatvédelmi hozzájárulásokkal',
            'mail' => [
                'subject' => 'A(z) :organization tagsági kérelmét rögzítettük!',
                'greeting' => 'Tisztelt :name!',
                'line1' => 'Megkaptuk tagsági kérelmét. Kérjük, erősítse meg e-mail címét a folytatáshoz.',
                'action' => 'E-mail cím megerősítése',
                'expires' => 'A link 48 óráig érvényes',
                'line2' => 'Az e-mail cím megerősítésével a(z) :organization tagsági kérelmét benyújtjuk.',
            ],
        ],
        'pending' => [
            'title' => 'Tagsági kérelem',
            'text' => 'Köszönjük kérelmét. Hamarosan e-mailt kap tőlünk, hogy megerősíthesse megadott e-mail címét.',
        ],
        'validation' => [
            'email' => [
                'application_pending' => 'Ezzel az e-mail címmel már benyújtottak tagsági kérelmet.',
                'already_member' => 'Ez az e-mail cím már tagként van regisztrálva.',

            ],
        ],
        'done' => [
            'title' => 'Kész 🎉',
            'text' => 'Kérelmét sikeresen benyújtottuk. Köszönjük! Jelentkezni fogunk Önnél.',
        ],
        'discount' => [
            'label' => 'Kedvezményes tagdíj kérelmezése',
            'reason' => [
                'label' => 'A kedvezmény oka',
            ],
        ],
        'fee' => [
            'text' => 'Tájékoztattak a havi :sum EUR tagdíjról, és kötelezettséget vállalok a fizetésre.',
            'label' => 'A fizető tagoknak havonta :sum EUR összeget kell fizetniük. A 75 év feletti tagok mentesülnek a tagdíjfizetés alól.',
            'payment' => [
                'banktt' => 'Az esedékes tagdíjat a megadott számlára kell befizetni.',
                'paypals' => 'A tagdíj a PayPal-számlák egyikére küldhető. Kérjük, válassza az "Ismerősöknek pénz küldése" módszert, különben 1,8% díjat von le a PayPal.',
                'paypal' => 'A tagdíj a(z) :iban PayPal-számlára küldhető. Kérjük, válassza az "Ismerősöknek pénz küldése" módszert, különben 1,8% díjat vonnak le.',
            ],
        ],
        'full_fee' => [
            'label' => 'A fizető tagoknak havonta :sum EUR összeget kell fizetniük.',
        ],
        'discounted_fee' => [
            'label' => 'A tagok csökkentett havi :sum EUR tagdíjat kérvényezhetnek.',
        ],
        'free_fee' => [
            'label' => 'A(z) :age év feletti tagok mentesülnek a tagdíjfizetés alól.',
        ],
        'email' => [
            'none' => 'Nincs e-mail címem!',
            'without' => [
                'text' => 'Ha nincs e-mail címe, nyomtassa ki ezt az űrlapot, írja alá, és küldje el postai úton a következő címre:',
            ],
            'benefits' => 'Az e-mail címmel rendelkező tagok automatikusan értesítéseket kapnak a rendezvényekről és hozzáférnek a Faliújsághoz.',
            'note' => [
                'header' => 'Fontos!',
                'content' => 'A webes programon keresztüli továbbításhoz meg kell adnia e-mail címét. Ha nincs e-mail címe, válassza a postai szolgáltatást.',
            ],
        ],
        'checkAndSubmit' => 'Információk ellenőrzése és űrlap beküldése',
        'printAndSubmit' => 'Űrlap nyomtatása',
        'title' => 'Tagsági kérelem a(z) :name egyesületben',
        'text' => 'Örülünk, hogy tagja szeretne lenni a(z) :name egyesületnek.',
        'process' => 'A felvétel az alábbi eljárás szerint történik:',
        'step1' => [
            'label' => '1. lépés',
            'text' => 'Első lépésként töltse ki az alábbi űrlapot.',
        ],
        'via' => [
            'web' => 'Küldés weben keresztül',
            'postal' => 'Postai küldés',
        ],
        'step2' => [
            'label' => '2. lépés',
            'text' => 'Ellenőrizze adatait',
        ],
        'click' => [
            'button' => 'Kattintson a gombra',
            'checkbox' => 'Kattintson a négyzetre',
        ],
        'step3a' => [
            'label' => '3a. lépés',
            'text' => 'Első lépésként töltse ki az alábbi űrlapot.',
        ],
        'step3b' => [
            'label' => '3b. lépés',
            'text' => 'Kattintson a „Űrlap nyomtatása” gombra.',
        ],
        'step4a' => [
            'label' => '4a. lépés',
            'text' => 'Kapni fog egy e-mailt a rendszertől egy egyszeri megerősítő linkkel.',
        ],
        'step4b' => [
            'label' => '4b. lépés',
            'text' => 'Kattintson az [Űrlap nyomtatása] gombra az űrlap PDF-változatának elkészítéséhez.',
        ],
        'step5a' => [
            'label' => '5a. lépés',
            'text' => 'A linkre kattintva megerősíti, hogy a regisztráció valóban Öntől származik.',
        ],
        'step5b' => [
            'label' => '5b. lépés',
            'text' => 'Nyomtassa ki az űrlapot, írja alá, és küldje el az űrlapon megadott címre.',
        ],
        'step6' => [
            'label' => '6. lépés',
            'text' => 'Ellenőrizzük adatait, és személyesen felvesszük Önnel a kapcsolatot, ha további információkra van szükség.',
        ],
        'step7' => [
            'label' => '7. lépés',
            'text' => 'Végül a vezetőség dönt a felvételéről, és az Ön által választott módon (e-mailben vagy postai úton) értesítést kap.',
        ],
        'submission' => [
            'success' => [
                'head' => 'Siker!',
                'msg' => 'Megkaptuk jelentkezését és ellenőrizzük. Köszönjük!',
            ],
            'failed' => [
                'head' => 'Hiba!',
                'msg' => 'Sajnos hiba történt. Kérjük, próbálja újra.',
            ],
        ],
        'print' => [
            'title' => 'Tagsági kérelem a(z) :name egyesületben',
            'greeting' => 'Tisztelt Hölgyeim és Uraim!',
            'text' => 'Ezúton kérem felvételemet a(z) :name egyesületbe',
            'regards' => 'Üdvözlettel',
            'overview' => [
                'person' => 'Rólam',
                'contact' => 'Elérhetőségeim',
            ],
            'filename' => 'Tagsagi_kerrelem_Magyar_Kolonia_Berlin_mid-:id:tm.pdf',
        ],
    ],
    'birth_date' => 'Születési dátum',
    'birth_place' => 'Születési hely',
    'name' => 'Vezetéknév',
    'first_name' => 'Keresztnév',
    'email' => 'E-mail',
    'phone' => 'Telefon',
    'mobile' => 'Mobil szám',
    'address' => 'Cím',
    'zip' => 'Irányítószám',
    'city' => 'Város',
    'country' => 'Ország',
    'locale' => 'Előnyben részesített nyelv',
    'gender' => 'Nem',
    'deduction_reason' => 'Idősebb, mint :age év',
    'type' => [
        'label' => 'Tagsági típus',
        'exempt' => 'Kizárva',
        'standard' => 'Tag',
        'applicant' => 'Jelölt',
        'board' => 'Vezetőség',
        'advisor' => 'Tanácsadó',
    ],
    'linked_user' => 'Felhasználói fiókhoz kapcsolva',
    'unlink_user' => 'Kapcsolat megszüntetése',
    'left_at' => 'Kilépés dátuma',
    'section' => [
        'admins' => 'A vezetőség tölti ki',
        'person' => 'Személy',
        'address' => 'Lakcím',
        'phone' => 'Telefon',
        'fees' => 'Tagdíj',
        'payments' => 'Befizetések',
        'deduction' => 'Kedvezmény',
        'email' => 'E-mail cím',
    ],
    'update' => [
        'success' => [
            'title' => 'Siker',
            'content' => 'A tag adatai sikeresen frissítve.',
        ],
    ],
    'date' => [
        'applied_at' => 'Tagság kérelmezve',
        'verified_at' => 'E-mail ellenőrizve',
        'entered_at' => 'Tagság megerősítve',
        'left_at' => 'Kilépve',
        'gdpr_consent_at' => 'Adatvédelem megerősítve',
        'newsletter_consent_at' => 'Hírlevél megerősítve',
        'photo_consent_at' => 'Fotó/Videó megerősítve',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => 'Ellenőrzési emlékeztető küldése',
        ],
        'addMember' => 'Új létrehozása',
        'sendAcceptanceMail' => [
            'label' => 'Kérelem elfogadása és e-mail küldése',
        ],
        'sendAcceptance' => [
            'label' => 'Kérelem elfogadása',
        ],
        'setEnteredAt' => [
            'label' => 'Elfogadva',
        ],
        'inviteAsUser' => [
            'label' => 'Tag meghívása felhasználóként',
        ],
        'cancelMembership' => [
            'label' => 'Tagság lemondása',
        ],
    ],
    'accordion' => [
        'optionals' => [
            'label' => 'Opcionális adatok',
        ],
    ],
    'appliance_received' => [
        'mail' => [
            'subject' => 'Tagsági kérelmét megkaptuk!',
            'greeting' => 'Szia :name,',
            'text' => 'Megkaptuk tagsági kérelmét, és köszönjük érdeklődését közösségünk iránt. Kérelmét a lehető leghamarabb megvizsgáljuk, és értesítjük Önt.',
        ],
    ],
    'cancel' => [
        'modal' => [
            'title' => 'Tagság lemondása',
            'text' => 'Kérjük, erősítse meg a tagság lemondását.',
        ],
        'confirm_text_input' => [
            'label' => 'A megerősítéshez adja meg a vezetéknevet',
        ],
        'btn' => [
            'final' => [
                'label' => 'Tagság végleges lemondása',
            ],
        ],
    ],
    'optional-data' => [
        'text' => 'Itt további adatok adhatók meg.',
    ],
    'familystatus' => [
        'label' => 'Családi állapot',
        'single' => 'Nőtlen/Hajadon',
        'married' => 'Házas',
        'divorced' => 'Elvált',
        'n_a' => 'Nem kíván válaszolni',
    ],
    'show' => [
        'title' => 'Tag áttekintése: :name',
        'created_at' => 'Létrehozva',
        'updated_at' => 'Utoljára szerkesztve',
        'about' => 'Személyes adatok',
        'membership' => 'Tagság',
        'change_requests' => 'Módosítási kérelmek',
        'payments' => 'Befizetések',
        'store' => 'Mentés',
        'payments_made' => 'Teljesített befizetések',
        'new_payment' => 'Új befizetés rögzítése',
        'payment_label' => 'Szöveg',
        'amount' => 'Összeg',
        'receipts' => 'Bizonylatok',
        'delete_user' => 'Felhasználó törlése!',
        'documents' => 'Dokumentumok',
        'fee_msg' => [
            'exempted' => 'Tagdíjmentes',
            'paid' => 'Tagdíj befizetve',
        ],
        'invitation_sent' => 'Meghívó elküldve',
        'member' => [
            'reactivate' => 'Tag újraaktiválása',
        ],
        'select_user' => 'Felhasználó kiválasztása',
        'empty_user_list' => 'Nincs felhasználó',
        'heading' => 'Tag adatainak megjelenítése',
        'attached' => [
            'success' => [
                'head' => 'Siker!',
                'msg' => 'A(z) :name felhasználó hozzárendelése sikeresen megtörtént.',
            ],
            'placeholder' => 'Felhasználó kiválasztása',
            'failed' => [
                'head' => 'Hiba!',
                'msg' => 'A felhasználó hozzárendelése nem sikerült.',
            ],
        ],
        'detached' => [
            'success' => [
                'head' => 'Siker!',
                'msg' => 'A(z) :name felhasználó hozzárendelése sikeresen eltávolítva.',
            ],
        ],
    ],
    'register' => [
        'title' => 'Jelszó beállítása a regisztrációhoz',
        'page_title' => 'Regisztráció befejezése',
        'password_requirements' => 'A jelszónak meg kell felelnie az alábbi követelményeknek:',
        'password' => 'Jelszó',
        'password_confirm' => 'Jelszó megerősítése',
        'submit' => 'Regisztráció befejezése',
        'checkLength' => 'Legalább 8 karakter',
        'checkCapital' => 'Legalább egy nagybetű',
        'checkNumbers' => 'Legalább egy szám',
        'checkSpecial' => 'Legalább egy speciális karakter (!"$§%(){}[])',
    ],
    'notifications' => [
        'new_applicant' => [
            'intro' => 'Új kérelem',
            'subject' => 'Új kérelem',
            'text' => 'Új kérelem érkezett.',
            'cta' => 'Megtekintés az irányítópulton',
            'reply_subject' => 'Tagsági kérelme a(z) :name egyesületben',
        ],
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'Új tagsági kérelmek',
            'empty_search' => 'Nincs megfelelő bejegyzés',
            'empty_list' => 'Nincs nyitott kérelem',
            'modal' => [
                'title' => 'Kérelem megtekintése',
                'reject' => [
                    'title' => 'Elutasítás',
                    'subtitle' => 'Az elutasítást indokolni kell',
                    'reason_label' => 'Indoklás',
                    'reason_placeholder' => 'Sajnos jelentkezését ...',
                    'confirm_btn' => 'Elutasítás küldése',
                ],
                'fields' => [
                    'applied_at' => 'Jelentkezett: :date',
                    'email' => 'E-mail',
                    'birth_date' => 'Születésnap',
                    'phone' => 'Telefon',
                    'address' => 'Lakcím',
                    'gdpr' => 'Adatvédelem',
                    'newsletter' => 'Hírlevél',
                    'photo_consent' => 'Fotó/Videó',

                ],
                'btn' => [
                    'cancel' => 'Mégse',
                    'reject' => 'Elutasítás',
                    'accept' => 'Elfogadás',
                ],
            ],
            'confirm' => [
                'deletion' => [
                    'title' => 'Siker',
                    'text' => 'A kiválasztott kérelmek törlésre kerültek',
                ],
            ],
            'options' => [
                'label' => 'Opciók',
                'deletion' => [
                    'confirm' => 'Kérjük, erősítse meg a kiválasztott kérelmek törlését!',
                    'btn' => [
                        'label' => 'Törlés',
                    ],
                ],
                'edit' => [
                    'btn' => [
                        'label' => 'Szerkesztés',
                    ],
                ],
            ],
            'search' => [
                'label' => 'Kérelmek keresése',
            ],
            'tab' => [
                'header' => [
                    'from' => 'Dátum',
                    'name' => 'Név',
                ],
            ],
        ],
    ],
    'application' => [
        'errors' => [
            'name-required' => 'Kérjük, adja meg a vezetéknevet',
        ],
    ],
    'index' => [
        'search-placeholder' => 'Keresés',
        'filter_by_status' => 'Szűrés státusz szerint',
    ],
    'create' => [
        'title' => 'Tag létrehozása',
        'account_label' => 'Számla: :name',
        'message' => [
            'success' => 'Tag sikeresen létrehozva',
            'fail' => 'A tag létrehozása nem sikerült. Kérdezze meg az adminisztrátort a naplóbejegyzésekről!',
        ],
    ],
    'backend' => [
        'cancel' => [
            'success' => [
                'head' => 'Tagság lemondva',
                'msg' => 'A tagság sikeresen lemondásra került.',
            ],
            'forbidden' => [
                'head' => 'Nincs jogosultság',
                'msg' => 'Nincs jogosultsága ezt a tagságot lemondani. (:error)',
            ],
            'modal' => [
                'title' => 'Tagság lemondása',
                'subtitle' => ':name tagságának lemondása. Ez a művelet nem vonható vissza.',
                'date_label' => 'Kilépés dátuma',
                'confirm' => 'Lemondás most',
            ],
        ],

        'pseudonymize' => [
            'success' => [
                'head' => 'Tag pszeudonimizálva',
                'msg' => 'A tag adatai sikeresen pszeudonimizálva lettek.',
            ],
            'forbidden' => [
                'head' => 'Nincs jogosultság',
                'msg' => 'Nincs jogosultsága ezt a tagot pszeudonimizálni. (:error)',
            ],
            'modal' => [
                'title' => 'Tag pszeudonimizálása',
                'subtitle' => ':name minden személyes adata visszavonhatatlanul törlődik.',
                'confirm' => 'Pszeudonimizálás most',
            ],
            'scheduled' => [
                'head' => 'Automatikus pszeudonimizálás',
                'msg' => ':count tag pszeudonimizálva lett.',
            ],
        ],
        'create' => [
            'heading' => 'Új tag létrehozása',
            'btn' => [
                'submit' => 'Tag rögzítése',
            ],
        ],
        'form' => [
            'no-user-found' => 'Nincs felhasználó',
        ],
        'attach' => [
            'failed' => [
                'head' => 'Hiba',
                'msg' => 'Felhasználó hozzárendelése nem sikerült.',
            ],
        ],
        'invitation' => [
            'sent' => [
                'head' => 'Siker',
                'msg' => 'Meghívó elküldve.',
            ],
            'failed' => [
                'head' => 'Hiba',
                'msg' => 'Meghívó nem lett elküldve: :error',
            ],
        ],
        'application' => [
            'accepted' => [
                'head' => 'Siker',
                'msg' => 'Tagság elfogadva.',
            ],
        ],
        'delete' => [
            'success' => [
                'head' => 'Siker',
                'msg' => 'Tagság lemondva.',
            ],
            'user_deleted' => [
                'msg' => 'Felhasználó törölve.',
            ],
            'user_failed' => [
                'msg' => 'Hiba a(z) :id felhasználó törlésekor.',
            ],
        ],

        'reactivate' => [
            'success' => [
                'head' => 'Siker',
                'msg' => 'Tagság visszaállítva.',
            ],
        ],
    ],
    'fees' => [
        'overview_title' => 'Tagdíjak áttekintése',
        'year' => 'Év',

        'search_member_placeholder' => 'Tag keresése...',
        'show_inactive' => 'Inaktívak megjelenítése',
        'pdf_export' => 'PDF export',
        'csv_export' => 'CSV export',

        'members' => 'Tagok',
        'paid' => 'Fizetve',
        'open' => 'Nyitott',
        'transactions' => 'Tranzakciók',
        'payments' => 'Befizetések',

        'member' => 'Tag',
        'type' => 'Típus',
        'date' => 'Dátum',
        'status' => 'Státusz',
        'receipt' => 'Bizonylat',

        'status_booked' => 'könyvelt',
        'status_submitted' => 'benyújtott',

        'send' => 'Küldés',
    ],
    'documents' => [

        'btn' => [
            'upload' => 'Dokumentum feltöltése',
            'save' => 'Mentés',
            'download' => 'Letöltés',
            'cancel' => 'Mégse',
        ],

        'upload' => [
            'title' => 'Új dokumentum feltöltése',
            'file_label' => 'Fájl (PDF, JPG, PNG, TIF)',
            'notes_label' => 'Megjegyzés (opcionális)',
        ],

        'category' => [
            'label' => 'Kategória',
            'placeholder' => 'Kategória választása…',
            'membership_form' => 'Tagsági kérelem',
            'sepa' => 'SEPA beszedési megbízás',
            'privacy' => 'Adatvédelmi nyilatkozat',
            'id_document' => 'Személyi azonosító',
            'other' => 'Egyéb',
        ],

        'table' => [
            'name' => 'Fájlnév',
            'category' => 'Kategória',
            'size' => 'Méret',
            'uploaded_by' => 'Feltöltötte',
            'last_accessed' => 'Utoljára megnyitva',
            'actions' => 'Műveletek',
        ],

        'confirm' => [
            'delete' => 'Valóban törölni szeretné a dokumentumot? Ez a művelet nem vonható vissza.',
        ],

        'upload_success' => 'A dokumentum sikeresen feltöltésre került.',
        'delete_success' => 'A dokumentum törlésre került.',
        'empty' => 'Ehhez a taghoz még nincsenek dokumentumok.',

        'errors' => [
            'unauthorized' => 'Nincs jogosultsága ehhez a művelethez.',
            'upload_failed' => 'Hiba történt a feltöltés során. Kérjük, próbálja újra.',
            'file_not_found' => 'A fájl nem található a tárolóban.',
            'invalid_file_type' => 'Csak PDF, JPG, PNG és TIF/TIFF fájlok engedélyezettek.',
            'file_too_large' => 'A fájl maximum 10 MB méretű lehet.',
            'mime_not_allowed_for_category' => 'Ez a fájltípus nem engedélyezett a kiválasztott kategóriához.',
        ],

    ],
    'export' => [
        'title' => 'Tagok exportálása',
        'description' => 'Válassza ki az export típusát és a kívánt szűrőket. A letöltés a gombra kattintás után indul.',
        'type_label' => 'Export típus',
        'filter_label' => 'Szűrő',
        'preview_count' => 'Tagok, amelyek megfelelnek a szűrési feltételeknek',
        'btn_download' => 'Letöltés',
        'btn_download_empty' => 'Nincs találat',
        'btn_label' => 'Export',
        'type' => [
            'stammdaten' => 'Törzsadatok',
            'stammdaten_desc' => 'Név, cím, elérhetőségek',
            'members_all' => 'Összes tagadat',
            'members_all_desc' => 'Minden mező, beleértve a szerepköröket, tagdíj típust és tagsági státuszt',
            'full' => 'Teljes export (ZIP)',
            'full_desc' => 'Minden adat + csatolt dokumentumok ZIP archívumban',
        ],

        'filter' => [
            'only_active' => 'Csak aktív tagok (nincs kilépési dátum)',
            'include_pseudonymized' => 'Pszeudonimizált tagok beleértve',
            'member_types' => 'Tagtípusok',
        ],
    ],
    'import' => [
        'btn_label' => 'Import',
        'page_title' => 'Tagok importálása',
        'mail' => [
            'subject' => 'Tagimport befejezve',
            'heading' => 'Import befejezve',
            'greeting' => 'Szia :name,',
            'intro' => 'A(z) :date -i tagimport sikeresen befejeződött.',
            'imported' => 'Importálva',
            'skipped' => 'Kihagyva (duplikátumok)',
            'errors' => 'Hibák',
            'duration' => 'Időtartam',
            'error_details' => 'Hiba részletek',
            'error_row' => ':row. sor',
            'backup_info' => 'Az import előtt biztonsági mentés készült a tagadatokról.',
            'backup_download' => 'Biztonsági mentés letöltése',
            'backup_expiry' => 'A letöltési link 24 óráig érvényes.',
            'footer' => 'Kérdések esetén forduljon az adminisztrátorhoz.',
            'failed_subject' => 'Tagimport sikertelen',
            'failed_heading' => 'Import sikertelen',
            'failed_greeting' => 'Szia :name,',
            'failed_intro' => 'A tagimport sajnos nem sikerült befejezni.',
            'failed_footer' => 'Kérjük, ellenőrizze a ZIP fájlt és próbálja újra.',

        ],
        'title' => 'Tagok importálása',
        'description' => 'Tagadatok importálása CSV vagy ZIP fájlból.',
        'btn_back' => 'Vissza',
        'btn_cancel' => 'Mégse',

        'upload' => [
            'title' => 'Fájl feltöltése',
            'description' => 'Válassza ki az import típusát és töltse fel a megfelelő fájlt.',
            'type_label' => 'Import típus',
            'file_label_csv' => 'CSV fájl kiválasztása',
            'file_label_zip' => 'ZIP fájl kiválasztása',
            'zip_hint' => 'A ZIP fájlok hitelességét ellenőrizzük (checksum). Csak a CommuCore-ból exportált fájlok fogadhatók el.',
            'error_heading' => 'Hiba a beolvasás során',
            'btn_upload' => 'Fájl beolvasása',
            'btn_uploading' => 'Beolvasás…',
            'dropzone_heading_csv' => 'CSV fájl ide húzása vagy kattintson',
            'dropzone_heading_zip' => 'ZIP fájl ide húzása vagy kattintson',
            'remove_file' => 'Fájl eltávolítása',
            'zip_async_hint' => 'A ZIP importálások a háttérben kerülnek feldolgozásra. E-mailt kap, amikor az import befejeződött.',
            'zip_job_dispatched' => 'Import elindítva',
            'zip_job_description' => 'A ZIP fájl feldolgozása a háttérben történik. E-mailt kap, amint az import befejeződött.',
            'template_hint' => 'Még nincs fájlja? Töltsön le egy üres sablont:',
            'template_download' => 'CSV sablon letöltése',
        ],

        'mapping' => [
            'title' => 'Mezők hozzárendelése',
            'description' => 'Rendelje hozzá a CSV fájl oszlopait a CommuCore mezőihez.',
            'col_csv' => 'CSV oszlop',
            'col_commucore' => 'CommuCore mező',
            'fields_mapped' => 'Mezők hozzárendelve',
            'btn_confirm' => 'Hozzárendelés megerősítése',
            'enum_modal_title' => 'Ismeretlen értékek hozzárendelése',
            'enum_modal_description' => 'A következő értékeket nem sikerült automatikusan hozzárendelni. Kérjük, rendelje hozzá őket manuálisan, vagy válassza az "Figyelmen kívül hagyás" lehetőséget.',
            'enum_skip' => 'Figyelmen kívül hagyás',
            'enum_modal_confirm' => 'Hozzárendelés elfogadása',
        ],

        'preview' => [
            'title' => 'Előnézet és biztonsági mentés',
            'description' => ':total sor található, :duplicates duplikátum felismerve.',
            'total_rows' => 'Sorok összesen',
            'new_rows' => 'Új',
            'duplicate_rows' => 'Duplikátumok',
            'duplicate' => 'Duplikátum',
            'new' => 'Új',
            'more_rows' => '… és további :count sor',
            'backup_required' => 'Biztonsági mentés szükséges',
            'backup_description' => 'Az import előtt automatikusan biztonsági mentés készül a jelenlegi tagadatokról.',
            'backup_created' => 'Biztonsági mentés létrehozva',
            'backup_download' => 'Biztonsági mentés letöltése',
            'btn_backup' => 'Biztonsági mentés létrehozása és tovább',
            'btn_backup_loading' => 'Biztonsági mentés létrehozása…',
            'btn_continue' => 'Import indítása',
        ],

        'log' => [
            'skipped' => [
                'label' => 'Kihagyva',
                'duplicate' => 'Duplikátum',
                'error' => 'Hiba',
            ],
            'completed' => [
                'label' => 'Import befejezve',
            ],
        ],

        'import' => [
            'title' => 'Import végrehajtása',
            'description' => ':count tag importálása.',
            'warning_heading' => 'Figyelem',
            'warning_text' => 'Az import nem vonható vissza automatikusan. A visszaállítás csak a létrehozott biztonsági mentésen keresztül lehetséges.',
            'confirm' => 'Valóban elindítja az importot?',
            'btn_start' => ':count tag importálása',
            'in_progress' => 'Import folyamatban…',
            'success_heading' => 'Import sikeresen befejezve',
            'btn_finish' => 'Befejezés',
            'rollback_confirm' => 'Valóban végrehajtja a visszaállítást? Minden importált adat törlődik.',
            'btn_rollback' => 'Visszaállítás végrehajtása',
            'btn_rolling_back' => 'Visszaállítás folyamatban…',
        ],
    ],
    'status' => [
        'active' => 'Aktív',
        'inactive' => 'Kilépett',
    ],
];
