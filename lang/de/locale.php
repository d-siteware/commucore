<?php

declare(strict_types=1);

return [
    'validation' => [
        'name' => [
            'required' => 'Name ist erforderlich.',
            'unique' => 'Diese Sprache existiert bereits.',
        ],
        'label' => [
            'required' => 'Label ist erforderlich.',
        ],
    ],
];
