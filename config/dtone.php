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

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Number of times to retry a failed request and the delay (in ms) between
    | retries. Set retries to 0 to disable.
    |
    */
    'retries' => env('DTONE_RETRIES', 0),
    'retry_delay' => env('DTONE_RETRY_DELAY', 100),
];
