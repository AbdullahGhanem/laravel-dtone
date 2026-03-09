<?php

namespace Ghanem\Dtone;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Request
{
    public static function request(string $method, string $endpoint, array $params = []): array
    {
        $key = config('dtone.is_production') ? config('dtone.key') : config('dtone.test_key');
        $secret = config('dtone.is_production') ? config('dtone.secret') : config('dtone.test_secret');
        $domain = config('dtone.is_production')
            ? 'https://dvs-api.dtone.com/v1/'
            : 'https://preprod-dvs-api.dtone.com/v1/';

        $isList = $params['list_api'] ?? false;
        unset($params['list_api']);

        $retries = (int) config('dtone.retries', 0);
        $retryDelay = (int) config('dtone.retry_delay', 100);

        $http = Http::withBasicAuth($key, $secret);

        if ($retries > 0) {
            $http = $http->retry($retries, $retryDelay);
        }

        $response = $http->$method($domain . $endpoint, $params);

        // Rate limiting awareness
        $remaining = $response->header('X-RateLimit-Remaining');
        if ($remaining !== null && (int) $remaining === 0) {
            $resetAt = $response->header('X-RateLimit-Reset');
            if ($resetAt) {
                $waitMs = max(0, ((int) $resetAt - time()) * 1000);
                if ($waitMs > 0 && $waitMs <= 60000) {
                    usleep($waitMs * 1000);
                }
            }
        }

        if ($isList) {
            return [
                'data' => $response->json(),
                'meta' => [
                    'total' => $response->header('X-Total'),
                    'total_pages' => $response->header('X-Total-Pages'),
                    'per_page' => $response->header('X-Per-Page'),
                    'page' => $response->header('X-Page'),
                    'next_page' => $response->header('X-Next-Page'),
                    'prev_page' => $response->header('X-Prev-Page'),
                ],
            ];
        }

        return $response->json() ?? [];
    }

    /**
     * Make a cached GET request for discovery endpoints.
     *
     * @param string   $cacheKey
     * @param string   $endpoint
     * @param array    $params
     * @param int|null $ttl Cache TTL in seconds (null = use config default)
     * @return array
     */
    public static function cachedRequest(string $cacheKey, string $endpoint, array $params = [], ?int $ttl = null): array
    {
        $cacheTtl = $ttl ?? (int) config('dtone.cache_ttl', 0);

        if ($cacheTtl <= 0) {
            return self::request('get', $endpoint, $params);
        }

        $cacheParams = $params;
        unset($cacheParams['list_api']);
        $fullKey = 'dtone:' . $cacheKey . ':' . md5(serialize($cacheParams));

        return Cache::remember($fullKey, $cacheTtl, function () use ($endpoint, $params) {
            return self::request('get', $endpoint, $params);
        });
    }

    /**
     * Clear all DT One cache or a specific endpoint cache.
     *
     * @param string|null $endpoint
     * @return void
     */
    public static function clearCache(?string $endpoint = null)
    {
        if ($endpoint) {
            // Clear specific endpoint pattern - works with tagged cache or prefix
            Cache::forget('dtone:' . $endpoint);
        }

        // For stores that support tags
        try {
            Cache::getStore();
            // Flush keys matching dtone: prefix by forgetting known endpoints
            $endpoints = ['services', 'countries', 'operators', 'products', 'campaigns', 'promotions', 'benefit-types', 'balances'];
            foreach ($endpoints as $ep) {
                // We can't enumerate all possible param combinations, but we clear the base
                Cache::forget('dtone:' . $ep . ':' . md5(serialize([])));
            }
        } catch (\Exception $e) {
            // Silently fail if cache store doesn't support the operation
        }
    }

    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

    public static function services(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        $ttl = (int) (config('dtone.cache_ttl_services') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('services', 'services', $params, $ttl);
    }

    public static function serviceById(int $id): array
    {
        $ttl = (int) (config('dtone.cache_ttl_services') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('services:' . $id, 'services/' . $id, [], $ttl);
    }

    // -------------------------------------------------------------------------
    // Countries
    // -------------------------------------------------------------------------

    public static function countries(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        $ttl = (int) (config('dtone.cache_ttl_countries') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('countries', 'countries', $params, $ttl);
    }

    public static function countryByIsoCode(string $iso_code): array
    {
        $ttl = (int) (config('dtone.cache_ttl_countries') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('countries:' . $iso_code, 'countries/' . $iso_code, [], $ttl);
    }

    // -------------------------------------------------------------------------
    // Operators
    // -------------------------------------------------------------------------

    public static function operators(?string $country_iso_code = null, ?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        if ($country_iso_code !== null) $params['country_iso_code'] = $country_iso_code;
        $params['list_api'] = true;

        $ttl = (int) (config('dtone.cache_ttl_operators') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('operators', 'operators', $params, $ttl);
    }

    public static function operatorById(int $id): array
    {
        $ttl = (int) (config('dtone.cache_ttl_operators') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('operators:' . $id, 'operators/' . $id, [], $ttl);
    }

    // -------------------------------------------------------------------------
    // Balances
    // -------------------------------------------------------------------------

    public static function balances(): array
    {
        return self::request('get', 'balances');
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    public static function products(
        ?string $type = null,
        ?int $service_id = null,
        ?string $country_iso_code = null,
        array $benefit_types = [],
        ?int $page = null,
        ?int $per_page = null
    ): array {
        $params = [];
        if (count($benefit_types)) $params['benefit_types'] = $benefit_types;
        if ($type !== null) $params['type'] = $type;
        if ($service_id !== null) $params['service_id'] = $service_id;
        if ($country_iso_code !== null) $params['country_iso_code'] = $country_iso_code;
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        $ttl = (int) (config('dtone.cache_ttl_products') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('products', 'products', $params, $ttl);
    }

    public static function productById(int $id): array
    {
        $ttl = (int) (config('dtone.cache_ttl_products') ?? config('dtone.cache_ttl', 0));

        return self::cachedRequest('products:' . $id, 'products/' . $id, [], $ttl);
    }

    // -------------------------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------------------------

    public static function campaigns(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::cachedRequest('campaigns', 'campaigns', $params);
    }

    public static function campaignById(int $id): array
    {
        return self::cachedRequest('campaigns:' . $id, 'campaigns/' . $id);
    }

    // -------------------------------------------------------------------------
    // Promotions
    // -------------------------------------------------------------------------

    public static function promotions(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::cachedRequest('promotions', 'promotions', $params);
    }

    public static function promotionById(int $id): array
    {
        return self::cachedRequest('promotions:' . $id, 'promotions/' . $id);
    }

    // -------------------------------------------------------------------------
    // Benefit Types
    // -------------------------------------------------------------------------

    public static function benefitTypes(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::cachedRequest('benefit-types', 'benefit-types', $params);
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    public static function transactions(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::request('get', 'transactions', $params);
    }

    public static function transactionById(int $id): array
    {
        return self::request('get', 'transactions/' . $id);
    }

    public static function createTransaction(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false): array
    {
        $params = [
            'external_id' => $external_id,
            'product_id' => $product_id,
            'credit_party_identifier' => $credit_party_identifier,
            'auto_confirm' => $auto_confirm,
        ];

        return self::request('post', 'async/transactions', $params);
    }

    public static function createTransactionSync(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false): array
    {
        $params = [
            'external_id' => $external_id,
            'product_id' => $product_id,
            'credit_party_identifier' => $credit_party_identifier,
            'auto_confirm' => $auto_confirm,
        ];

        return self::request('post', 'sync/transactions', $params);
    }

    public static function confirmTransaction(int $transaction_id): array
    {
        return self::request('post', 'async/transactions/' . $transaction_id . '/confirm');
    }

    public static function confirmTransactionSync(int $transaction_id): array
    {
        return self::request('post', 'sync/transactions/' . $transaction_id . '/confirm');
    }

    public static function cancelTransaction(int $transaction_id): array
    {
        return self::request('post', 'async/transactions/' . $transaction_id . '/cancel');
    }

    // -------------------------------------------------------------------------
    // Lookups
    // -------------------------------------------------------------------------

    public static function lookupOperatorsByMobileNumber(string $mobile_number): array
    {
        $params['list_api'] = true;

        return self::request('get', 'lookup/mobile-number/' . $mobile_number, $params);
    }

    public static function statementInquiry(int $product_id, array $credit_party_identifier): array
    {
        $params = [
            'product_id' => $product_id,
            'credit_party_identifier' => $credit_party_identifier,
        ];

        return self::request('post', 'lookup/statement-inquiry', $params);
    }

    public static function creditPartyBenefits(int $product_id, array $credit_party_identifier): array
    {
        $params = [
            'product_id' => $product_id,
            'credit_party_identifier' => $credit_party_identifier,
        ];

        return self::request('post', 'lookup/credit-party/benefits', $params);
    }

    public static function creditPartyStatus(int $product_id, array $credit_party_identifier): array
    {
        $params = [
            'product_id' => $product_id,
            'credit_party_identifier' => $credit_party_identifier,
        ];

        return self::request('post', 'lookup/credit-party/status', $params);
    }
}
