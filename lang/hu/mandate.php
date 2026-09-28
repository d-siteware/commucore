<?php

declare(strict_types=1);

return [
    'title' => 'SEPA közvetlen terhelési meghatalmazás',
    'debtor' => 'Fizetésre kötelezett',
    'name' => 'Név',
    'street' => 'Utca',
    'zip_city' => 'Irányítószám/Város',
    'country' => 'Ország',
    'creditor' => 'Hitelező',
    'mandate_text' => 'Felhatalmazom a hitelezőt, hogy közvetlen terheléssel fizetéseket vonjon le a számlámról. Egyben felhatalmazom a hitelintézetemet, hogy a hitelező által a számlámra kiállított terheléseket teljesítse.',
    'mandate_type' => 'A terhelés típusa',
    'mandate_type_core' => 'Alapterhelés (CORE)',
    'mandate_type_b2b' => 'Vállalati terhelés (B2B)',
    'hint' => 'Megjegyzés',
    'hint_core' => 'A terhelés dátumától számított nyolc héten belül visszatérítést kérhetek a terhelt összegről. A hitelintézetemmel kötött megállapodás feltételei érvényesek.',
    'hint_b2b' => 'Mivel ez SEPA vállalati terhelés (B2B), a terhelés teljesítése után nem vagyok jogosult a terhelt összeg visszatérítésére. Megerősítem, hogy a meghatalmazás kiállításakor nem fogyasztóként járok el.',
    'fee_info' => 'Tagdíj információ (csak tájékoztató jellegű, nem része a meghatalmazásnak)',
    'fee_amount' => 'Tagdíj összege',
    'fee_interval' => 'Gyakoriság',
    'fee_per_year' => 'Éves várható terhelések száma',
    'fee_hint' => 'Megjegyzés: Ezek az információk csak tájékoztató jellegűek. A SEPA közvetlen terhelési meghatalmazás lehetővé teszi a hitelező számára, hogy az összegtől és gyakoriságtól függetlenül minden esedékes fizetést levonjon.',
    'location_date' => 'Hely, Dátum',
    'signature' => 'Aláírás',
];
