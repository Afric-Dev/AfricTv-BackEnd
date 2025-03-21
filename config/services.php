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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
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

    'deezer' => [
        'base_uri' => env('DEEZER_API_URL', 'https://api.deezer.com/'),
    ],

   'shazam' => [
        'key' => env('SHAZAM_API_KEY'),
        'base_uri' => env('SHAZAM_BASE_URL'),
    ],

    'replicate' => [
        'base_uri' => 'https://api.replicate.com/v1/',
        'key' => env('REPLICATE_API_KEY'),
    ],
    'listenbrainz' => [
        'api_token' => env('LISTENBRAINZ_API_TOKEN'),
    ],
    
    'justwatch' => [
        'api_url' => env('JUSTWATCH_API_URL'),
        'partner_token' => env('JUSTWATCH_PARTNER_TOKEN'),
    ],

    'rapidapi' => [
        'host_job' => env('RAPIDAPI_JOB'),
        'key' => env('RAPIDAPI_KEY'),
    ],  
];
