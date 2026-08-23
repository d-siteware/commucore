<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'verify_email' => 'Verify email',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'sso_error' => 'SSO error',
    'sso_retry' => 'Login again',

    'account_deleted' => [
        'title' => 'Your access has been deleted',
        'text' => 'Your user account has been removed. If this was a mistake or you want to get back in, contact us — we will set up your access again.',
        'cta' => 'Contact support',
        'home' => 'Back to homepage',
    ],

    'register' => [
        'page_title' => 'Register',
        'btn' => 'Register',
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'terms' => 'T&C',
    ],

    'api_tokens' => [
        'create' => 'Create API Token',
        'create_btn' => 'Create',
        'description' => 'API tokens allow third-party services to authenticate with our application on your behalf.',
        'token_name' => 'Token Name',
        'permissions' => 'Permissions',
        'created' => 'Created.',
        'manage' => 'Manage API Tokens',
        'manage_description' => 'You may delete any of your existing tokens if they are no longer needed.',
        'last_used' => 'Last used',
        'delete' => 'Delete',
        'api_token' => 'API Token',
        'copy_token' => 'Please copy your new API token. For your security, it won\'t be shown again.',
        'close' => 'Close',
        'token_permissions' => 'API Token Permissions',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'delete_token' => 'Delete API Token',
        'delete_confirm' => 'Are you sure you would like to delete this API token?',
    ],

];
