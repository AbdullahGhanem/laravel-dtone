<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\Request;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class RequestTest extends TestCase
{
    private function fakeListResponse(array $data = [['id' => 1]]): \GuzzleHttp\Promise\PromiseInterface
    {
        return Http::response($data, 200, [
            'X-Total' => '10',
            'X-Total-Pages' => '2',
            'X-Per-Page' => '5',
            'X-Page' => '1',
            'X-Next-Page' => '2',
            'X-Prev-Page' => '',
        ]);
    }

    public function test_services_sends_get_request(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::services(1, 5);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertEquals('10', $result['meta']['total']);
        $this->assertEquals('2', $result['meta']['total_pages']);
        $this->assertEquals('1', $result['meta']['page']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'preprod-dvs-api.dtone.com/v1/services')
                && $request->hasHeader('Authorization');
        });
    }

    public function test_service_by_id_sends_correct_endpoint(): void
    {
        Http::fake(['*' => Http::response(['id' => 42, 'name' => 'Test Service'])]);

        $result = Request::serviceById(42);

        $this->assertEquals(42, $result['id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'services/42');
        });
    }

    public function test_countries_sends_get_request(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['iso_code' => 'US', 'name' => 'United States']])]);

        $result = Request::countries();

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
    }

    public function test_country_by_iso_code(): void
    {
        Http::fake(['*' => Http::response(['iso_code' => 'US', 'name' => 'United States'])]);

        $result = Request::countryByIsoCode('US');

        $this->assertEquals('US', $result['iso_code']);
    }

    public function test_operators_with_country_filter(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::operators('US', 1, 50);

        $this->assertArrayHasKey('data', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'country_iso_code=US');
        });
    }

    public function test_operator_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 5, 'name' => 'Test Operator'])]);

        $result = Request::operatorById(5);

        $this->assertEquals(5, $result['id']);
    }

    public function test_balances(): void
    {
        Http::fake(['*' => Http::response([['amount' => 100, 'currency' => 'USD']])]);

        $result = Request::balances();

        $this->assertIsArray($result);
    }

    public function test_products_with_filters(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::products('FIXED_VALUE_RECHARGE', 1, 'US', ['Airtime'], 1, 10);

        $this->assertArrayHasKey('data', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'products')
                && str_contains($request->url(), 'type=FIXED_VALUE_RECHARGE');
        });
    }

    public function test_product_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 99, 'name' => 'Test Product'])]);

        $result = Request::productById(99);

        $this->assertEquals(99, $result['id']);
    }

    public function test_transactions_list(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::transactions(1, 50);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_create_transaction_sends_post(): void
    {
        Http::fake(['*' => Http::response(['id' => 123, 'status' => 'CREATED'])]);

        $result = Request::createTransaction('ext-123', 99, ['mobile_number' => '+1234567890']);

        $this->assertEquals(123, $result['id']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'async/transactions');
        });
    }

    public function test_lookup_operators_by_mobile_number(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::lookupOperatorsByMobileNumber('+1234567890');

        $this->assertArrayHasKey('data', $result);
    }

    public function test_production_url_when_configured(): void
    {
        config(['dtone.is_production' => true]);

        Http::fake(['*' => Http::response([['amount' => 100]])]);

        Request::balances();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'dvs-api.dtone.com')
                && ! str_contains($request->url(), 'preprod');
        });
    }

    public function test_uses_sandbox_credentials_in_test_mode(): void
    {
        config(['dtone.is_production' => false]);

        Http::fake(['*' => Http::response([])]);

        Request::balances();

        Http::assertSent(function ($request) {
            $auth = $request->header('Authorization')[0] ?? '';
            $decoded = base64_decode(str_replace('Basic ', '', $auth));
            return $decoded === 'test-sandbox-key:test-sandbox-secret';
        });
    }

    public function test_uses_production_credentials_in_production_mode(): void
    {
        config(['dtone.is_production' => true]);

        Http::fake(['*' => Http::response([])]);

        Request::balances();

        Http::assertSent(function ($request) {
            $auth = $request->header('Authorization')[0] ?? '';
            $decoded = base64_decode(str_replace('Basic ', '', $auth));
            return $decoded === 'test-key:test-secret';
        });
    }

    public function test_create_transaction_with_auto_confirm(): void
    {
        Http::fake(['*' => Http::response(['id' => 124, 'status' => 'CONFIRMED'])]);

        $result = Request::createTransaction('ext-124', 99, ['mobile_number' => '+1234567890'], true);

        $this->assertEquals(124, $result['id']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->method() === 'POST'
                && ($body['auto_confirm'] ?? false) === true;
        });
    }

    public function test_confirm_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 123, 'status' => 'CONFIRMED'])]);

        $result = Request::confirmTransaction(123);

        $this->assertEquals('CONFIRMED', $result['status']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), '123/confirm');
        });
    }

    public function test_list_api_param_not_sent_to_api(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        Request::services();

        Http::assertSent(function ($request) {
            return ! str_contains($request->url(), 'list_api');
        });
    }
}
