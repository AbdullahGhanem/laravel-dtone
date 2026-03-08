<?php

namespace Ghanem\Dtone\Events;

class TransactionStatusChanged
{
    /** @var array */
    public $payload;

    /** @var string|null */
    public $status;

    /** @var int|null */
    public $transactionId;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->status = $payload['status'] ?? null;
        $this->transactionId = $payload['id'] ?? null;
    }
}
