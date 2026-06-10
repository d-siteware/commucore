<?php

declare(strict_types=1);

return [

    // -------------------------------------------------------------------------
    // Buttons
    // -------------------------------------------------------------------------
    'btn' => [
        'upload' => 'Upload document',
        'save' => 'Save',
        'saving' => 'Saving...',
        'cancel' => 'Cancel',
        'download' => 'Download',
    ],

    // -------------------------------------------------------------------------
    // Upload form
    // -------------------------------------------------------------------------
    'upload' => [
        'title' => 'Upload documents',
        'file_label' => 'Files',
        'file_hint' => 'PDF, Word, Excel, images or .eml – max. 20 MB per file',
        'label_field' => 'Label',
        'label_placeholder' => 'e.g. Funding decision 2024',
        'notes_label' => 'Note',
        'loading' => 'Preparing files...',
        'drag_hint' => 'Drag files here or click to upload',
    ],

    // -------------------------------------------------------------------------
    // Category
    // -------------------------------------------------------------------------
    'category' => [
        'label' => 'Category',
        'placeholder' => 'Choose category (optional)',
    ],

    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------
    'table' => [
        'name' => 'Filename',
        'category' => 'Category',
        'size' => 'Size',
        'uploaded_by' => 'Uploaded by',
        'last_accessed' => 'Last accessed',
        'actions' => 'Actions',
    ],

    // -------------------------------------------------------------------------
    // Confirmations
    // -------------------------------------------------------------------------
    'confirm' => [
        'delete' => 'Really delete document? This action cannot be undone.',
    ],

    // -------------------------------------------------------------------------
    // Messages
    // -------------------------------------------------------------------------
    'empty' => 'No documents yet.',
    'upload_success' => ':count document uploaded successfully.|:count documents uploaded successfully.',
    'upload_partial_failure' => ':count file(s) could not be saved.',
    'delete_success' => 'Document has been deleted.',

    // -------------------------------------------------------------------------
    // Errors
    // -------------------------------------------------------------------------
    'errors' => [
        'unauthorized' => 'No permission for this action.',
        'file_not_found' => 'File not found.',
        'no_files' => 'Please select at least one file.',
        'mime_not_allowed' => 'This file type is not allowed for the selected category.',
        'upload_failed' => 'An error occurred while saving.',
    ],
];
