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
    'rapidapi' => [
        'key' => env('RAPIDAPI_KEY'),
    ],
    'mpesa' => [
        'env'             => env('MPESA_ENVIRONMENT', 'sandbox'), 
        'short_code'      => env('MPESA_SHORTCODE', '174379'),
        'pass_key'        => env('MPESA_PASSKEY'),
        'consumer_key'    => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'callback'        => env('MPESA_CALLBACK'),                
    ],
    'email' => [
        'admin_email' => env('ADMIN_EMAIL', 'ombenifaraja@gmail.com'),
    ],


];
