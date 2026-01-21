<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'meta' => [
        'phone_id' => env('META_PHONE_ID'),
        'token' => env('META_WHATSAPP_TOKEN'),
        'verify_token' => env('META_VERIFY_TOKEN', 'Thiago'),
        'whatsapp_number' => env('META_WHATSAPP_NUMBER', '558183797797'),
        'app_secret' => env('META_APP_SECRET'),
        'skip_signature_validation' => env('META_SKIP_SIGNATURE_VALIDATION', false),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

];
