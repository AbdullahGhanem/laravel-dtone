<?php

namespace Ghanem\Dtone\Http\Controllers;

use Ghanem\Dtone\Events\TransactionCancelled;
use Ghanem\Dtone\Events\TransactionCompleted;
use Ghanem\Dtone\Events\TransactionConfirmed;
use Ghanem\Dtone\Events\TransactionFailed;
use Ghanem\Dtone\Events\TransactionStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController
{
    /**
     * Handle an incoming DT One webhook callback.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        if (config('dtone.webhook_logging', false)) {
            Log::info('DT One webhook received', $payload);
        }

        $status = $payload['status'] ?? null;

        event(new TransactionStatusChanged($payload));

        switch ($status) {
            case 'COMPLETED':
                event(new TransactionCompleted($payload));
                break;
            case 'FAILED':
            case 'DECLINED':
                event(new TransactionFailed($payload));
                break;
            case 'CONFIRMED':
                event(new TransactionConfirmed($payload));
                break;
            case 'CANCELLED':
                event(new TransactionCancelled($payload));
                break;
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
