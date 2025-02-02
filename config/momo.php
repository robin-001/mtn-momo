<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MTN MoMo API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your MTN MoMo API credentials and settings.
    | Make sure to keep these values secure and never commit them to version control.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    */

    // Your MTN MoMo API Key
    'api_key' => env('MOMO_API_KEY'),

    // Your MTN MoMo API User
    'api_user' => env('MOMO_API_USER'),

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */

    // The base URL for the MTN MoMo API
    'api_base_url' => env('MOMO_API_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),

    // The environment to use (sandbox or production)
    'environment' => env('MOMO_ENVIRONMENT', 'sandbox'),
];
