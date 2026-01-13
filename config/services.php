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

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'price_one_time' => env('STRIPE_PRICE_ONE_TIME'),
        'price_monthly' => env('STRIPE_PRICE_MONTHLY'),
    ],

    'payment' => [
        'success_url' => env('PAYMENT_SUCCESS_URL'),
    ],

    'teachable' => [
        'api_key' => env('TEACHABLE_API_KEY'),
        'course_id' => env('TEACHABLE_COURSE_ID'),
        'base_url' => 'https://developers.teachable.com',
    ],

    'facebook' => [
        'pixel_id' => env('FB_PIXEL_ID'),
        'access_token' => env('FB_ACCESS_TOKEN'),
        'test_event_code' => env('FB_TEST_EVENT_CODE'),
        'api_version' => 'v21.0',
    ],

];
