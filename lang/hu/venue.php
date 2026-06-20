<?php

declare(strict_types=1);

return [
    'name' => 'Név',
    'address' => 'Cím',
    'postal_code' => 'Irányítószám',
    'city' => 'Város',
    'country' => 'Ország',
    'phone' => 'Telefon',
    'website' => 'Weboldal',
    'geolocation' => 'Geolokáció',

    'geolocation.more' => 'több',
    'geolocation.hint' => 'A kód beírása a Google Maps-ben mutatót helyez el a helyszínre a navigáció indításához.',

    'new.heading' => 'Új helyszín',
    'new.btn.label' => 'Új',
    'edit.heading' => 'Helyszín szerkesztése',
    'edit.btn.label' => 'Szerkesztés',

    'form.save_only' => 'Csak mentés',
    'form.save_and_apply' => 'Mentés + Alkalmazás',

    'toast.created.heading' => 'Helyszín létrehozva',
    'toast.created.text' => 'A helyszín sikeresen elmentve.',
    'toast.updated.heading' => 'Helyszín frissítve',
    'toast.updated.text' => 'A módosítások sikeresen elmentve.',
    'toast.deleted.heading' => 'Törölve',
    'toast.deleted.text' => 'A helyszín eltávolítva.',

    'tool' => [
        'heading' => 'Helyszínek kezelése',
        'create' => 'Új helyszín',
        'edit' => 'Szerkesztés',
        'delete' => 'Törlés',
        'search_placeholder' => 'Keresés név, város vagy cím alapján…',
        'events_count' => 'Rendezvények',
        'empty' => 'Még nincsenek helyszínek létrehozva.',

        'delete_confirm' => [
            'heading' => 'Helyszín törlése?',
            'text' => ':name véglegesen törölve lesz.',
            'in_use_heading' => 'Figyelem: Még használatban van',
            'in_use_text' => ':name még :count rendezvényben van használatban. A meglévő rendezvényekben megmarad, de új rendezvényekhez nem rendelhető.|:name még :count rendezvényben van használatban. A meglévő rendezvényekben megmarad, de új rendezvényekhez nem rendelhető.',
            'cancel' => 'Mégse',
            'confirm' => 'Végleges törlés',
        ],
    ],
];
