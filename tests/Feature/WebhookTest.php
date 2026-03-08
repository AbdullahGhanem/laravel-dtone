<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\Events\TransactionCancelled;
use Ghanem\Dtone\Events\TransactionCompleted;
use Ghanem\Dtone\Events\TransactionConfirmed;
use Ghanem\Dtone\Events\TransactionFailed;
use Ghanem\Dtone\Events\TransactionStatusChanged;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class WebhookTest extends TestCase
{
    public function test_webhook_route_exists(): void
    {
        $response = $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'COMPLETED']);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_webhook_dispatches_status_changed_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'COMPLETED']);

        Event::assertDispatched(TransactionStatusChanged::class, function ($event) {
            return $event->transactionId === 1 && $event->status === 'COMPLETED';
        });
    }

    public function test_webhook_dispatches_completed_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'COMPLETED']);

        Event::assertDispatched(TransactionCompleted::class);
    }

    public function test_webhook_dispatches_failed_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'FAILED']);

        Event::assertDispatched(TransactionFailed::class);
    }

    public function test_webhook_dispatches_declined_as_failed_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'DECLINED']);

        Event::assertDispatched(TransactionFailed::class);
    }

    public function test_webhook_dispatches_confirmed_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'CONFIRMED']);

        Event::assertDispatched(TransactionConfirmed::class);
    }

    public function test_webhook_dispatches_cancelled_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'CANCELLED']);

        Event::assertDispatched(TransactionCancelled::class);
    }

    public function test_webhook_signature_verification_passes(): void
    {
        config(['dtone.webhook_secret' => 'test-secret']);

        $payload = json_encode(['id' => 1, 'status' => 'COMPLETED']);
        $signature = hash_hmac('sha256', $payload, 'test-secret');

        $response = $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'COMPLETED'], [
            'X-Dtone-Signature' => $signature,
        ]);

        $response->assertStatus(200);
    }

    public function test_webhook_with_unknown_status_only_dispatches_generic_event(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', ['id' => 1, 'status' => 'PROCESSING']);

        Event::assertDispatched(TransactionStatusChanged::class);
        Event::assertNotDispatched(TransactionCompleted::class);
        Event::assertNotDispatched(TransactionFailed::class);
        Event::assertNotDispatched(TransactionConfirmed::class);
        Event::assertNotDispatched(TransactionCancelled::class);
    }

    public function test_event_contains_full_payload(): void
    {
        Event::fake();

        $this->postJson('dtone/webhook', [
            'id' => 123,
            'status' => 'COMPLETED',
            'external_id' => 'ext-1',
            'product_id' => 99,
        ]);

        Event::assertDispatched(TransactionCompleted::class, function ($event) {
            return $event->payload['external_id'] === 'ext-1'
                && $event->payload['product_id'] === 99;
        });
    }
}
