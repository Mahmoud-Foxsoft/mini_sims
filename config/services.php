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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'nowPayments' => [
        'api_url' => env('NOW_PAYMENTS_API_URL'),
        'api_key' => env('NOW_PAYMENTS_API_KEY'),
        'fee' => env('NOW_PAYMENTS_FEE'),
    ],

    'centralServer' => [
        'api_url' => env('CENTRAL_SERVER_API_URL'),
        'api_key' => env('CENTRAL_SERVER_API_KEY'),
        'payment_url' => env('CENTRAL_SERVER_PAYMENT_URL'),
        'phone_services_url' => env('CENTRAL_SERVER_PHONE_SERVICES_URL'),
        'phone_numbers_url' => env('CENTRAL_SERVER_PHONE_NUMBERS_URL'),
    ],
];
