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

    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Countries
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Operators
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Balances
    // -------------------------------------------------------------------------

    public function test_balances(): void
    {
        Http::fake(['*' => Http::response([['amount' => 100, 'currency' => 'USD']])]);

        $result = Request::balances();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------------------------

    public function test_campaigns_list(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::campaigns(1, 10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/campaigns');
        });
    }

    public function test_campaign_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 7, 'name' => 'Summer Campaign'])]);

        $result = Request::campaignById(7);

        $this->assertEquals(7, $result['id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'campaigns/7');
        });
    }

    // -------------------------------------------------------------------------
    // Promotions
    // -------------------------------------------------------------------------

    public function test_promotions_list(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::promotions(1, 10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/promotions');
        });
    }

    public function test_promotion_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 3, 'name' => 'Bonus Promo'])]);

        $result = Request::promotionById(3);

        $this->assertEquals(3, $result['id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'promotions/3');
        });
    }

    // -------------------------------------------------------------------------
    // Benefit Types
    // -------------------------------------------------------------------------

    public function test_benefit_types_list(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['name' => 'Airtime']])]);

        $result = Request::benefitTypes();

        $this->assertArrayHasKey('data', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'benefit-types');
        });
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    public function test_transactions_list(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::transactions(1, 50);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_transaction_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 456, 'status' => 'COMPLETED'])]);

        $result = Request::transactionById(456);

        $this->assertEquals(456, $result['id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'transactions/456');
        });
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

    public function test_create_transaction_sync(): void
    {
        Http::fake(['*' => Http::response(['id' => 125, 'status' => 'COMPLETED'])]);

        $result = Request::createTransactionSync('ext-125', 99, ['mobile_number' => '+1234567890']);

        $this->assertEquals(125, $result['id']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'sync/transactions');
        });
    }

    public function test_confirm_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 123, 'status' => 'CONFIRMED'])]);

        $result = Request::confirmTransaction(123);

        $this->assertEquals('CONFIRMED', $result['status']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'async/transactions/123/confirm');
        });
    }

    public function test_confirm_transaction_sync(): void
    {
        Http::fake(['*' => Http::response(['id' => 123, 'status' => 'CONFIRMED'])]);

        $result = Request::confirmTransactionSync(123);

        $this->assertEquals('CONFIRMED', $result['status']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'sync/transactions/123/confirm');
        });
    }

    public function test_cancel_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 123, 'status' => 'CANCELLED'])]);

        $result = Request::cancelTransaction(123);

        $this->assertEquals('CANCELLED', $result['status']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), '123/cancel');
        });
    }

    // -------------------------------------------------------------------------
    // Lookups
    // -------------------------------------------------------------------------

    public function test_lookup_operators_by_mobile_number(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Request::lookupOperatorsByMobileNumber('+1234567890');

        $this->assertArrayHasKey('data', $result);
    }

    public function test_statement_inquiry(): void
    {
        Http::fake(['*' => Http::response(['balance' => 500, 'currency' => 'USD'])]);

        $result = Request::statementInquiry(99, ['account_number' => '123456']);

        $this->assertEquals(500, $result['balance']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'lookup/statement-inquiry');
        });
    }

    public function test_credit_party_benefits(): void
    {
        Http::fake(['*' => Http::response(['remaining' => 10])]);

        $result = Request::creditPartyBenefits(99, ['mobile_number' => '+1234567890']);

        $this->assertEquals(10, $result['remaining']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'lookup/credit-party/benefits');
        });
    }

    public function test_credit_party_status(): void
    {
        Http::fake(['*' => Http::response(['status' => 'ACTIVE'])]);

        $result = Request::creditPartyStatus(99, ['mobile_number' => '+1234567890']);

        $this->assertEquals('ACTIVE', $result['status']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), 'lookup/credit-party/status');
        });
    }

    // -------------------------------------------------------------------------
    // Config & Auth
    // -------------------------------------------------------------------------

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

    public function test_list_api_param_not_sent_to_api(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        Request::services();

        Http::assertSent(function ($request) {
            return ! str_contains($request->url(), 'list_api');
        });
    }
}
