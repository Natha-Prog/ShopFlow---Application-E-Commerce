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

    'mvola' => [
        'api_key' => env('MVOLA_API_KEY'),
        'api_secret' => env('MVOLA_API_SECRET'),
        'merchant_number' => env('MVOLA_MERCHANT_NUMBER'),
        'partner_name' => env('MVOLA_PARTNER_NAME', config('app.name')),
        'environment' => env('MVOLA_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('MVOLA_WEBHOOK_SECRET'),
    ],

    'orange_money' => [
        'api_key' => env('ORANGE_MONEY_API_KEY'),
        'api_secret' => env('ORANGE_MONEY_API_SECRET'),
        'merchant_number' => env('ORANGE_MONEY_MERCHANT_NUMBER'),
        'environment' => env('ORANGE_MONEY_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('ORANGE_MONEY_WEBHOOK_SECRET'),
    ],

    'airtel_money' => [
        'api_key' => env('AIRTEL_MONEY_API_KEY'),
        'api_secret' => env('AIRTEL_MONEY_API_SECRET'),
        'merchant_number' => env('AIRTEL_MONEY_MERCHANT_NUMBER'),
        'environment' => env('AIRTEL_MONEY_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('AIRTEL_MONEY_WEBHOOK_SECRET'),
    ],

];
