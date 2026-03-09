<?php

namespace Ghanem\Dtone\Tests\Unit;

use Ghanem\Dtone\Request;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CacheTest extends TestCase
{
    public function test_discovery_endpoints_are_cached_when_ttl_set(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response([['id' => 1, 'name' => 'Mobile']], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        $result1 = Request::services();
        $result2 = Request::services();

        $this->assertEquals($result1, $result2);

        // Only one HTTP request should be made (second served from cache)
        Http::assertSentCount(1);
    }

    public function test_cache_disabled_when_ttl_is_zero(): void
    {
        config(['dtone.cache_ttl' => 0]);

        Http::fake(['*' => Http::response([['id' => 1]], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::services();
        Request::services();

        Http::assertSentCount(2);
    }

    public function test_per_endpoint_cache_ttl_override(): void
    {
        config(['dtone.cache_ttl' => 0, 'dtone.cache_ttl_services' => 300]);

        Http::fake(['*' => Http::response([['id' => 1]], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::services();
        Request::services();

        Http::assertSentCount(1);
    }

    public function test_different_params_create_different_cache_keys(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response([['id' => 1]], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::services(1, 10);
        Request::services(2, 10);

        Http::assertSentCount(2);
    }

    public function test_transactions_are_never_cached(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response([['id' => 1]], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::transactions();
        Request::transactions();

        Http::assertSentCount(2);
    }

    public function test_clear_cache(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response([['id' => 1]], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::services();
        Request::clearCache();
        Request::services();

        Http::assertSentCount(2);
    }

    public function test_countries_are_cached(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response([['iso_code' => 'US']], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        Request::countries();
        Request::countries();

        Http::assertSentCount(1);
    }

    public function test_service_by_id_is_cached(): void
    {
        config(['dtone.cache_ttl' => 300]);

        Http::fake(['*' => Http::response(['id' => 1, 'name' => 'Mobile'])]);

        Request::serviceById(1);
        Request::serviceById(1);

        Http::assertSentCount(1);
    }
}
