<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CommandTest extends TestCase
{
    public function test_balance_command(): void
    {
        Http::fake(['*' => Http::response([
            ['amount' => 100, 'currency' => 'USD'],
            ['amount' => 200, 'currency' => 'EUR'],
        ])]);

        $this->artisan('dtone:balance')
            ->expectsTable(['Amount', 'Currency'], [
                [100, 'USD'],
                [200, 'EUR'],
            ])
            ->assertExitCode(0);
    }

    public function test_balance_command_empty(): void
    {
        Http::fake(['*' => Http::response([])]);

        $this->artisan('dtone:balance')
            ->expectsOutputToContain('No balances found')
            ->assertExitCode(0);
    }

    public function test_products_command(): void
    {
        Http::fake(['*' => Http::response([
            ['id' => 1, 'name' => 'Recharge', 'type' => 'FIXED', 'operator' => ['name' => 'Op1']],
        ], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '10', 'X-Page' => '1',
        ])]);

        $this->artisan('dtone:products')
            ->expectsOutputToContain('Recharge')
            ->assertExitCode(0);
    }

    public function test_products_command_with_filters(): void
    {
        Http::fake(['*' => Http::response([
            ['id' => 1, 'name' => 'Recharge', 'type' => 'FIXED', 'operator' => ['name' => 'Op1']],
        ], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '10', 'X-Page' => '1',
        ])]);

        $this->artisan('dtone:products --country=US --type=FIXED')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'country_iso_code=US')
                && str_contains($request->url(), 'type=FIXED');
        });
    }

    public function test_transaction_command_list(): void
    {
        Http::fake(['*' => Http::response([
            ['id' => 123, 'external_id' => 'ext-1', 'status' => 'COMPLETED', 'creation_date' => '2024-01-01'],
        ], 200, [
            'X-Total' => '1', 'X-Total-Pages' => '1', 'X-Per-Page' => '10', 'X-Page' => '1',
        ])]);

        $this->artisan('dtone:transaction')
            ->expectsTable(['ID', 'External ID', 'Status', 'Created At'], [
                [123, 'ext-1', 'COMPLETED', '2024-01-01'],
            ])
            ->assertExitCode(0);
    }

    public function test_transaction_command_by_id(): void
    {
        Http::fake(['*' => Http::response([
            'id' => 456, 'external_id' => 'ext-2', 'status' => 'CONFIRMED', 'creation_date' => '2024-01-01',
        ])]);

        $this->artisan('dtone:transaction 456')
            ->expectsOutputToContain('456')
            ->expectsOutputToContain('CONFIRMED')
            ->assertExitCode(0);
    }

    public function test_cache_clear_command(): void
    {
        $this->artisan('dtone:cache-clear')
            ->expectsOutputToContain('Cleared all DT One cache')
            ->assertExitCode(0);
    }

    public function test_cache_clear_command_specific_endpoint(): void
    {
        $this->artisan('dtone:cache-clear services')
            ->expectsOutputToContain('Cleared cache for: services')
            ->assertExitCode(0);
    }

    public function test_health_command(): void
    {
        Http::fake(['*' => Http::response([
            ['amount' => 100, 'currency' => 'USD'],
        ], 200, [
            'X-Total' => '5', 'X-Total-Pages' => '1', 'X-Per-Page' => '50', 'X-Page' => '1',
        ])]);

        $this->artisan('dtone:health')
            ->expectsOutputToContain('OK')
            ->expectsOutputToContain('Health check passed')
            ->assertExitCode(0);
    }
}
