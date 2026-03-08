<?php

namespace Ghanem\Dtone\Tests\Unit;

use Ghanem\Dtone\Dto\Balance;
use Ghanem\Dtone\Dto\BenefitType;
use Ghanem\Dtone\Dto\Campaign;
use Ghanem\Dtone\Dto\Country;
use Ghanem\Dtone\Dto\Meta;
use Ghanem\Dtone\Dto\Operator;
use Ghanem\Dtone\Dto\PaginatedResponse;
use Ghanem\Dtone\Dto\Product;
use Ghanem\Dtone\Dto\Promotion;
use Ghanem\Dtone\Dto\Service;
use Ghanem\Dtone\Dto\Transaction;
use Ghanem\Dtone\Tests\TestCase;

class DtoTest extends TestCase
{
    public function test_service_from_array(): void
    {
        $dto = Service::fromArray(['id' => 1, 'name' => 'Mobile']);

        $this->assertEquals(1, $dto->getId());
        $this->assertEquals('Mobile', $dto->getName());
        $this->assertEquals(['id' => 1, 'name' => 'Mobile'], $dto->toArray());
    }

    public function test_country_from_array(): void
    {
        $dto = Country::fromArray(['iso_code' => 'US', 'name' => 'United States', 'regions' => [['name' => 'California']]]);

        $this->assertEquals('US', $dto->getIsoCode());
        $this->assertEquals('United States', $dto->getName());
        $this->assertCount(1, $dto->getRegions());
    }

    public function test_operator_from_array(): void
    {
        $dto = Operator::fromArray([
            'id' => 5,
            'name' => 'Test Op',
            'country' => ['iso_code' => 'US', 'name' => 'United States'],
        ]);

        $this->assertEquals(5, $dto->getId());
        $this->assertInstanceOf(Country::class, $dto->getCountry());
        $this->assertEquals('US', $dto->getCountry()->getIsoCode());
    }

    public function test_operator_without_country(): void
    {
        $dto = Operator::fromArray(['id' => 5, 'name' => 'Test Op']);

        $this->assertNull($dto->getCountry());
    }

    public function test_product_from_array(): void
    {
        $dto = Product::fromArray([
            'id' => 99,
            'name' => 'Recharge',
            'description' => 'Top up',
            'type' => 'FIXED_VALUE_RECHARGE',
            'service' => ['id' => 1, 'name' => 'Mobile'],
            'operator' => ['id' => 5, 'name' => 'Op'],
            'prices' => ['wholesale' => 10],
        ]);

        $this->assertEquals(99, $dto->getId());
        $this->assertEquals('FIXED_VALUE_RECHARGE', $dto->getType());
        $this->assertInstanceOf(Service::class, $dto->getService());
        $this->assertInstanceOf(Operator::class, $dto->getOperator());
        $this->assertEquals(['wholesale' => 10], $dto->getAttribute('prices'));
        $this->assertNull($dto->getAttribute('nonexistent'));
        $this->assertEquals('fallback', $dto->getAttribute('nonexistent', 'fallback'));
    }

    public function test_balance_from_array(): void
    {
        $dto = Balance::fromArray(['amount' => 150.50, 'currency' => 'EUR']);

        $this->assertEquals(150.50, $dto->getAmount());
        $this->assertEquals('EUR', $dto->getCurrency());
    }

    public function test_transaction_from_array(): void
    {
        $dto = Transaction::fromArray([
            'id' => 123,
            'external_id' => 'ext-1',
            'status' => 'COMPLETED',
            'product' => ['id' => 99],
        ]);

        $this->assertEquals(123, $dto->getId());
        $this->assertEquals('ext-1', $dto->getExternalId());
        $this->assertEquals('COMPLETED', $dto->getStatus());
        $this->assertEquals(['id' => 99], $dto->getAttribute('product'));
    }

    public function test_campaign_from_array(): void
    {
        $dto = Campaign::fromArray(['id' => 7, 'name' => 'Summer']);

        $this->assertEquals(7, $dto->getId());
        $this->assertEquals('Summer', $dto->getName());
    }

    public function test_promotion_from_array(): void
    {
        $dto = Promotion::fromArray([
            'id' => 3,
            'name' => 'Bonus',
            'operator' => ['id' => 5, 'name' => 'Op'],
        ]);

        $this->assertEquals(3, $dto->getId());
        $this->assertInstanceOf(Operator::class, $dto->getOperator());
    }

    public function test_promotion_without_operator(): void
    {
        $dto = Promotion::fromArray(['id' => 3, 'name' => 'Bonus']);

        $this->assertNull($dto->getOperator());
    }

    public function test_benefit_type_from_array(): void
    {
        $dto = BenefitType::fromArray(['name' => 'Airtime']);

        $this->assertEquals('Airtime', $dto->getName());
    }

    public function test_meta_from_array(): void
    {
        $dto = Meta::fromArray([
            'total' => '10',
            'total_pages' => '2',
            'per_page' => '5',
            'page' => '1',
            'next_page' => '2',
            'prev_page' => '',
        ]);

        $this->assertEquals(10, $dto->getTotal());
        $this->assertEquals(2, $dto->getTotalPages());
        $this->assertEquals(5, $dto->getPerPage());
        $this->assertEquals(1, $dto->getPage());
        $this->assertEquals(2, $dto->getNextPage());
        $this->assertEquals(0, $dto->getPrevPage());
    }

    public function test_meta_handles_missing_keys(): void
    {
        $dto = Meta::fromArray([]);

        $this->assertNull($dto->getTotal());
        $this->assertNull($dto->getPage());
    }

    public function test_paginated_response_from_array(): void
    {
        $result = PaginatedResponse::fromArray([
            'data' => [['id' => 1, 'name' => 'Mobile'], ['id' => 2, 'name' => 'Data']],
            'meta' => ['total' => '2', 'page' => '1'],
        ], Service::class);

        $this->assertCount(2, $result->getData());
        $this->assertInstanceOf(Service::class, $result->getData()[0]);
        $this->assertInstanceOf(Meta::class, $result->getMeta());
        $this->assertEquals(2, $result->getMeta()->getTotal());
    }

    public function test_paginated_response_to_array(): void
    {
        $result = PaginatedResponse::fromArray([
            'data' => [['id' => 1, 'name' => 'Mobile']],
            'meta' => ['total' => '1'],
        ], Service::class);

        $array = $result->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('meta', $array);
        $this->assertEquals(1, $array['data'][0]['id']);
        $this->assertEquals(1, $array['meta']['total']);
    }

    public function test_dto_handles_missing_fields_gracefully(): void
    {
        $service = Service::fromArray([]);
        $this->assertNull($service->getId());
        $this->assertNull($service->getName());

        $country = Country::fromArray([]);
        $this->assertNull($country->getIsoCode());
        $this->assertEquals([], $country->getRegions());

        $transaction = Transaction::fromArray([]);
        $this->assertNull($transaction->getId());
        $this->assertNull($transaction->getStatus());
    }
}
