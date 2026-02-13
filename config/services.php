<?php

return [
    'auth_app' => [
        'url' => env('AUTH_APP_URL'),
    ],

    'auth' => [
        'token_endpoint' => env('AUTH_TOKEN_ENDPOINT', 'https://auth.opinio.co.jp/api/oauth/token'),
        'client_id'      => env('AUTH_CLIENT_ID', 'ats'),
        'client_secret'  => env('AUTH_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_OAUTH_REDIRECT_URI', 'http://localhost/settings/gmail/callback'),
    ],

    'gmail_sync' => [
        'interval_minutes' => (int) env('GMAIL_SYNC_INTERVAL', 15),
        'batch_size' => (int) env('GMAIL_BATCH_SIZE', 50),
        'lookback_days' => (int) env('GMAIL_LOOKBACK_DAYS', 7),
    ],
];
