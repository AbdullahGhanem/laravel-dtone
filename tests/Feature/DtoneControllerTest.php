<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\DtoneController;
use Ghanem\Dtone\Facades\Dtone;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class DtoneControllerTest extends TestCase
{
    private function fakeListResponse(): \GuzzleHttp\Promise\PromiseInterface
    {
        return Http::response([['id' => 1]], 200, [
            'X-Total' => '1',
            'X-Total-Pages' => '1',
            'X-Per-Page' => '50',
            'X-Page' => '1',
        ]);
    }

    public function test_controller_services(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $controller = new DtoneController();
        $result = $controller->services();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_controller_countries(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $controller = new DtoneController();
        $result = $controller->countries(1, 10);

        $this->assertArrayHasKey('data', $result);
    }

    public function test_facade_services(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Dtone::services();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_facade_balances(): void
    {
        Http::fake(['*' => Http::response([['amount' => 100]])]);

        $result = Dtone::balances();

        $this->assertIsArray($result);
    }

    public function test_facade_create_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CREATED'])]);

        $result = Dtone::createTransaction('ext-1', 99, ['mobile_number' => '+123']);

        $this->assertEquals('CREATED', $result['status']);
    }

    public function test_facade_create_transaction_sync(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'COMPLETED'])]);

        $result = Dtone::createTransactionSync('ext-1', 99, ['mobile_number' => '+123']);

        $this->assertEquals('COMPLETED', $result['status']);
    }

    public function test_facade_confirm_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CONFIRMED'])]);

        $result = Dtone::confirmTransaction(1);

        $this->assertEquals('CONFIRMED', $result['status']);
    }

    public function test_facade_confirm_transaction_sync(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CONFIRMED'])]);

        $result = Dtone::confirmTransactionSync(1);

        $this->assertEquals('CONFIRMED', $result['status']);
    }

    public function test_facade_cancel_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CANCELLED'])]);

        $result = Dtone::cancelTransaction(1);

        $this->assertEquals('CANCELLED', $result['status']);
    }

    public function test_facade_campaigns(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Dtone::campaigns();

        $this->assertArrayHasKey('data', $result);
    }

    public function test_facade_promotions(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Dtone::promotions();

        $this->assertArrayHasKey('data', $result);
    }

    public function test_facade_benefit_types(): void
    {
        Http::fake(['*' => $this->fakeListResponse()]);

        $result = Dtone::benefitTypes();

        $this->assertArrayHasKey('data', $result);
    }

    public function test_facade_transaction_by_id(): void
    {
        Http::fake(['*' => Http::response(['id' => 456, 'status' => 'COMPLETED'])]);

        $result = Dtone::transactionById(456);

        $this->assertEquals(456, $result['id']);
    }

    public function test_facade_statement_inquiry(): void
    {
        Http::fake(['*' => Http::response(['balance' => 500])]);

        $result = Dtone::statementInquiry(99, ['account_number' => '123456']);

        $this->assertEquals(500, $result['balance']);
    }

    public function test_facade_credit_party_benefits(): void
    {
        Http::fake(['*' => Http::response(['remaining' => 10])]);

        $result = Dtone::creditPartyBenefits(99, ['mobile_number' => '+123']);

        $this->assertEquals(10, $result['remaining']);
    }

    public function test_facade_credit_party_status(): void
    {
        Http::fake(['*' => Http::response(['status' => 'ACTIVE'])]);

        $result = Dtone::creditPartyStatus(99, ['mobile_number' => '+123']);

        $this->assertEquals('ACTIVE', $result['status']);
    }
}
