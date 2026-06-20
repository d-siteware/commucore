<?php

declare(strict_types=1);

return [
    'name' => 'Name',
    'address' => 'Address',
    'postal_code' => 'Postal code',
    'city' => 'City',
    'country' => 'Country',
    'phone' => 'Phone',
    'website' => 'Website',
    'geolocation' => 'Geolocation',

    'geolocation.more' => 'more',
    'geolocation.hint' => 'Entering the code in Google Maps will place a marker on the location so you can start navigation.',

    'new.heading' => 'New venue',
    'new.btn.label' => 'New',
    'edit.heading' => 'Edit venue',
    'edit.btn.label' => 'Edit',

    'form.save_only' => 'Save only',
    'form.save_and_apply' => 'Save + Apply',

    'toast.created.heading' => 'Venue created',
    'toast.created.text' => 'The venue has been saved successfully.',
    'toast.updated.heading' => 'Venue updated',
    'toast.updated.text' => 'The changes have been saved successfully.',
    'toast.deleted.heading' => 'Deleted',
    'toast.deleted.text' => 'The venue has been removed.',

    'tool' => [
        'heading' => 'Manage venues',
        'create' => 'New venue',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'search_placeholder' => 'Search by name, city or address…',
        'events_count' => 'Events',
        'empty' => 'No venues created yet.',

        'delete_confirm' => [
            'heading' => 'Delete venue?',
            'text' => ':name will be permanently deleted.',
            'in_use_heading' => 'Warning: Still in use',
            'in_use_text' => ':name is still used in :count event. It will remain in existing events but cannot be assigned to new ones.|:name is still used in :count events. It will remain in existing events but cannot be assigned to new ones.',
            'cancel' => 'Cancel',
            'confirm' => 'Delete permanently',
        ],
    ],
];
