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

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the webhook endpoint for DT One transaction callbacks.
    | Set webhook_path to null to disable the webhook route.
    |
    */
    'webhook_path' => env('DTONE_WEBHOOK_PATH', 'dtone/webhook'),
    'webhook_secret' => env('DTONE_WEBHOOK_SECRET', ''),
    'webhook_middleware' => [],
    'webhook_logging' => env('DTONE_WEBHOOK_LOGGING', false),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL in seconds for discovery endpoints. Set to 0 to disable.
    | You can override the TTL per endpoint type.
    |
    */
    'cache_ttl' => env('DTONE_CACHE_TTL', 0),
    'cache_ttl_services' => env('DTONE_CACHE_TTL_SERVICES', null),
    'cache_ttl_countries' => env('DTONE_CACHE_TTL_COUNTRIES', null),
    'cache_ttl_operators' => env('DTONE_CACHE_TTL_OPERATORS', null),
    'cache_ttl_products' => env('DTONE_CACHE_TTL_PRODUCTS', null),
];
