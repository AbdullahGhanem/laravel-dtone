<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DT One Production API Credentials
    |--------------------------------------------------------------------------
    */
    'key' => env('DTONE_KEY', ''),
    'secret' => env('DTONE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | DT One Test/Sandbox API Credentials
    |--------------------------------------------------------------------------
    */
    'test_key' => env('DTONE_TEST_KEY', ''),
    'test_secret' => env('DTONE_TEST_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment Toggle
    |--------------------------------------------------------------------------
    |
    | Set to true to use production API, false for sandbox/preprod.
    |
    */
    'is_production' => env('DTONE_IS_PRODUCTION', false),
];
