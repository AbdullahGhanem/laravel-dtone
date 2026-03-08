<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\DtoneController;
use Ghanem\Dtone\Dto\Balance;
use Ghanem\Dtone\Dto\Campaign;
use Ghanem\Dtone\Dto\Country;
use Ghanem\Dtone\Dto\Operator;
use Ghanem\Dtone\Dto\PaginatedResponse;
use Ghanem\Dtone\Dto\Product;
use Ghanem\Dtone\Dto\Promotion;
use Ghanem\Dtone\Dto\Service;
use Ghanem\Dtone\Dto\Transaction;
use Ghanem\Dtone\Facades\Dtone;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class DtoneControllerTest extends TestCase
{
    private function fakeListResponse(array $data = [['id' => 1]]): \GuzzleHttp\Promise\PromiseInterface
    {
        return Http::response($data, 200, [
            'X-Total' => '1',
            'X-Total-Pages' => '1',
            'X-Per-Page' => '50',
            'X-Page' => '1',
        ]);
    }

    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

    public function test_controller_services_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 1, 'name' => 'Mobile']])]);

        $controller = new DtoneController();
        $result = $controller->services();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertCount(1, $result->getData());
        $this->assertInstanceOf(Service::class, $result->getData()[0]);
        $this->assertEquals(1, $result->getData()[0]->getId());
        $this->assertEquals('Mobile', $result->getData()[0]->getName());
    }

    public function test_facade_service_by_id_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 42, 'name' => 'Data'])]);

        $result = Dtone::serviceById(42);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertEquals(42, $result->getId());
    }

    // -------------------------------------------------------------------------
    // Countries
    // -------------------------------------------------------------------------

    public function test_facade_countries_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['iso_code' => 'US', 'name' => 'United States']])]);

        $result = Dtone::countries();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Country::class, $result->getData()[0]);
        $this->assertEquals('US', $result->getData()[0]->getIsoCode());
    }

    public function test_facade_country_by_iso_code_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['iso_code' => 'US', 'name' => 'United States'])]);

        $result = Dtone::countryByIsoCode('US');

        $this->assertInstanceOf(Country::class, $result);
        $this->assertEquals('United States', $result->getName());
    }

    // -------------------------------------------------------------------------
    // Operators
    // -------------------------------------------------------------------------

    public function test_facade_operators_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 5, 'name' => 'Operator 1']])]);

        $result = Dtone::operators();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Operator::class, $result->getData()[0]);
        $this->assertEquals(5, $result->getData()[0]->getId());
    }

    public function test_facade_operator_by_id_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 5, 'name' => 'Test Operator', 'country' => ['iso_code' => 'US', 'name' => 'United States']])]);

        $result = Dtone::operatorById(5);

        $this->assertInstanceOf(Operator::class, $result);
        $this->assertInstanceOf(Country::class, $result->getCountry());
        $this->assertEquals('US', $result->getCountry()->getIsoCode());
    }

    // -------------------------------------------------------------------------
    // Balances
    // -------------------------------------------------------------------------

    public function test_facade_balances_returns_balance_dtos(): void
    {
        Http::fake(['*' => Http::response([['amount' => 100, 'currency' => 'USD']])]);

        $result = Dtone::balances();

        $this->assertIsArray($result);
        $this->assertInstanceOf(Balance::class, $result[0]);
        $this->assertEquals(100, $result[0]->getAmount());
        $this->assertEquals('USD', $result[0]->getCurrency());
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    public function test_facade_products_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 99, 'name' => 'Product 1', 'type' => 'FIXED_VALUE_RECHARGE']])]);

        $result = Dtone::products();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Product::class, $result->getData()[0]);
        $this->assertEquals('FIXED_VALUE_RECHARGE', $result->getData()[0]->getType());
    }

    public function test_facade_product_by_id_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 99, 'name' => 'Test', 'service' => ['id' => 1, 'name' => 'Mobile']])]);

        $result = Dtone::productById(99);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertInstanceOf(Service::class, $result->getService());
    }

    // -------------------------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------------------------

    public function test_facade_campaigns_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 7, 'name' => 'Summer']])]);

        $result = Dtone::campaigns();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Campaign::class, $result->getData()[0]);
    }

    // -------------------------------------------------------------------------
    // Promotions
    // -------------------------------------------------------------------------

    public function test_facade_promotions_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 3, 'name' => 'Bonus']])]);

        $result = Dtone::promotions();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Promotion::class, $result->getData()[0]);
    }

    // -------------------------------------------------------------------------
    // Benefit Types
    // -------------------------------------------------------------------------

    public function test_facade_benefit_types_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['name' => 'Airtime']])]);

        $result = Dtone::benefitTypes();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    public function test_facade_transactions_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 1, 'status' => 'COMPLETED']])]);

        $result = Dtone::transactions();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Transaction::class, $result->getData()[0]);
    }

    public function test_facade_transaction_by_id_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 456, 'status' => 'COMPLETED', 'external_id' => 'ext-1'])]);

        $result = Dtone::transactionById(456);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals(456, $result->getId());
        $this->assertEquals('COMPLETED', $result->getStatus());
        $this->assertEquals('ext-1', $result->getExternalId());
    }

    public function test_facade_create_transaction_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CREATED', 'external_id' => 'ext-1'])]);

        $result = Dtone::createTransaction('ext-1', 99, ['mobile_number' => '+123']);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('CREATED', $result->getStatus());
    }

    public function test_facade_create_transaction_sync_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'COMPLETED'])]);

        $result = Dtone::createTransactionSync('ext-1', 99, ['mobile_number' => '+123']);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('COMPLETED', $result->getStatus());
    }

    public function test_facade_confirm_transaction_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CONFIRMED'])]);

        $result = Dtone::confirmTransaction(1);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('CONFIRMED', $result->getStatus());
    }

    public function test_facade_confirm_transaction_sync_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CONFIRMED'])]);

        $result = Dtone::confirmTransactionSync(1);

        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_facade_cancel_transaction_returns_dto(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CANCELLED'])]);

        $result = Dtone::cancelTransaction(1);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('CANCELLED', $result->getStatus());
    }

    // -------------------------------------------------------------------------
    // Lookups
    // -------------------------------------------------------------------------

    public function test_facade_lookup_operators_returns_paginated_response(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 1, 'name' => 'Operator']])]);

        $result = Dtone::lookupOperatorsByMobileNumber('+123');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertInstanceOf(Operator::class, $result->getData()[0]);
    }

    public function test_facade_statement_inquiry_returns_array(): void
    {
        Http::fake(['*' => Http::response(['balance' => 500])]);

        $result = Dtone::statementInquiry(99, ['account_number' => '123456']);

        $this->assertIsArray($result);
        $this->assertEquals(500, $result['balance']);
    }

    public function test_facade_credit_party_benefits_returns_array(): void
    {
        Http::fake(['*' => Http::response(['remaining' => 10])]);

        $result = Dtone::creditPartyBenefits(99, ['mobile_number' => '+123']);

        $this->assertIsArray($result);
    }

    public function test_facade_credit_party_status_returns_array(): void
    {
        Http::fake(['*' => Http::response(['status' => 'ACTIVE'])]);

        $result = Dtone::creditPartyStatus(99, ['mobile_number' => '+123']);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // toArray compatibility
    // -------------------------------------------------------------------------

    public function test_paginated_response_to_array(): void
    {
        Http::fake(['*' => $this->fakeListResponse([['id' => 1, 'name' => 'Mobile']])]);

        $result = Dtone::services();
        $array = $result->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('meta', $array);
        $this->assertIsArray($array['data'][0]);
    }

    public function test_transaction_to_array(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CREATED', 'external_id' => 'ext-1'])]);

        $result = Dtone::createTransaction('ext-1', 99, ['mobile_number' => '+123']);
        $array = $result->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
    }
}
