<?php

namespace Ghanem\Dtone\Tests\Feature;

use Ghanem\Dtone\Dto\Transaction;
use Ghanem\Dtone\Notifications\DtoneChannel;
use Ghanem\Dtone\Notifications\DtoneMessage;
use Ghanem\Dtone\Tests\TestCase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class NotificationTest extends TestCase
{
    public function test_dtone_message_builder(): void
    {
        $message = DtoneMessage::create(99)
            ->externalId('ext-1')
            ->toMobileNumber('+1234567890')
            ->autoConfirm()
            ->sync();

        $this->assertEquals(99, $message->getProductId());
        $this->assertEquals('ext-1', $message->getExternalId());
        $this->assertEquals(['mobile_number' => '+1234567890'], $message->getCreditPartyIdentifier());
        $this->assertTrue($message->getAutoConfirm());
        $this->assertTrue($message->isSync());
    }

    public function test_dtone_message_to_custom_identifier(): void
    {
        $message = DtoneMessage::create(99)
            ->to(['account_number' => '123456']);

        $this->assertEquals(['account_number' => '123456'], $message->getCreditPartyIdentifier());
    }

    public function test_dtone_message_defaults(): void
    {
        $message = DtoneMessage::create(99)
            ->toMobileNumber('+123');

        $this->assertNull($message->getExternalId());
        $this->assertFalse($message->getAutoConfirm());
        $this->assertFalse($message->isSync());
    }

    public function test_dtone_channel_sends_async_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CREATED', 'external_id' => 'ext-1'])]);

        $channel = new DtoneChannel();

        $notification = new class extends Notification {
            public function toDtone($notifiable)
            {
                return DtoneMessage::create(99)
                    ->externalId('ext-1')
                    ->toMobileNumber('+1234567890');
            }
        };

        $result = $channel->send(null, $notification);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals('CREATED', $result->getStatus());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'async/transactions');
        });
    }

    public function test_dtone_channel_sends_sync_transaction(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'COMPLETED'])]);

        $channel = new DtoneChannel();

        $notification = new class extends Notification {
            public function toDtone($notifiable)
            {
                return DtoneMessage::create(99)
                    ->externalId('ext-1')
                    ->toMobileNumber('+1234567890')
                    ->sync();
            }
        };

        $result = $channel->send(null, $notification);

        $this->assertEquals('COMPLETED', $result->getStatus());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sync/transactions');
        });
    }

    public function test_dtone_channel_sends_with_auto_confirm(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'status' => 'CONFIRMED'])]);

        $channel = new DtoneChannel();

        $notification = new class extends Notification {
            public function toDtone($notifiable)
            {
                return DtoneMessage::create(99)
                    ->externalId('ext-1')
                    ->toMobileNumber('+1234567890')
                    ->autoConfirm();
            }
        };

        $result = $channel->send(null, $notification);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return ($body['auto_confirm'] ?? false) === true;
        });
    }
}
