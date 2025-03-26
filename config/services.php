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

    'cmacgm' => [
        'client_id' => env('CMA_CGM_CLIENT_ID'),
        'client_secret' => env('CMA_CGM_CLIENT_SECRET'),
        'bearer_token' => env('CMA_CGM_CLIENT_BEARER_TOKEN'),
        'api_endpoint_v1' => env('CMA_CGM_API_ENDPOINT_V1'),
        'api_endpoint_v2' => env('CMA_CGM_API_ENDPOINT_V2'),
        'token_url' => env('CMA_CGM_TOKEN_URL'),
    ],

];
