<?php

namespace Ghanem\Dtone\Notifications;

use Ghanem\Dtone\Dto\Transaction;
use Ghanem\Dtone\Request;
use Illuminate\Notifications\Notification;

class DtoneChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     * @return Transaction
     */
    public function send($notifiable, Notification $notification)
    {
        /** @var DtoneMessage $message */
        $message = $notification->toDtone($notifiable);

        $externalId = $message->getExternalId() ?? uniqid('dtone_');
        $productId = $message->getProductId();
        $creditParty = $message->getCreditPartyIdentifier();
        $autoConfirm = $message->getAutoConfirm();

        if ($message->isSync()) {
            $result = Request::createTransactionSync($externalId, $productId, $creditParty, $autoConfirm);
        } else {
            $result = Request::createTransaction($externalId, $productId, $creditParty, $autoConfirm);
        }

        return Transaction::fromArray($result);
    }
}
