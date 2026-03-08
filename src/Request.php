<?php

namespace Ghanem\Dtone;

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

        $response = Http::withBasicAuth($key, $secret)->$method($domain . $endpoint, $params);

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

    public static function services(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::request('get', 'services', $params);
    }

    public static function serviceById(int $id): array
    {
        return self::request('get', 'services/' . $id);
    }

    public static function countries(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::request('get', 'countries', $params);
    }

    public static function countryByIsoCode(string $iso_code): array
    {
        return self::request('get', 'countries/' . $iso_code);
    }

    public static function operators(?string $country_iso_code = null, ?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        if ($country_iso_code !== null) $params['country_iso_code'] = $country_iso_code;
        $params['list_api'] = true;

        return self::request('get', 'operators', $params);
    }

    public static function operatorById(int $id): array
    {
        return self::request('get', 'operators/' . $id);
    }

    public static function balances(): array
    {
        return self::request('get', 'balances');
    }

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

        return self::request('get', 'products', $params);
    }

    public static function transactions(?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        if ($page !== null) $params['page'] = $page;
        if ($per_page !== null) $params['per_page'] = $per_page;
        $params['list_api'] = true;

        return self::request('get', 'transactions', $params);
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

    public static function confirmTransaction(int $transaction_id): array
    {
        return self::request('post', $transaction_id . '/confirm');
    }

    public static function productById(int $id): array
    {
        return self::request('get', 'products/' . $id);
    }

    public static function lookupOperatorsByMobileNumber(string $mobile_number): array
    {
        $params['list_api'] = true;

        return self::request('get', 'lookup/mobile-number/' . $mobile_number, $params);
    }
}
