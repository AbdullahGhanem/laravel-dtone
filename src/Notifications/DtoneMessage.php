<?php

namespace Ghanem\Dtone\Notifications;

class DtoneMessage
{
    /** @var string */
    private $externalId;
    /** @var int */
    private $productId;
    /** @var array */
    private $creditPartyIdentifier;
    /** @var bool */
    private $autoConfirm = false;
    /** @var bool */
    private $sync = false;

    /**
     * @param int $productId
     * @return static
     */
    public static function create($productId)
    {
        $message = new self();
        $message->productId = $productId;

        return $message;
    }

    /**
     * @param string $externalId
     * @return $this
     */
    public function externalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @param array $identifier
     * @return $this
     */
    public function to($identifier)
    {
        $this->creditPartyIdentifier = $identifier;

        return $this;
    }

    /**
     * @param string $mobileNumber
     * @return $this
     */
    public function toMobileNumber($mobileNumber)
    {
        $this->creditPartyIdentifier = ['mobile_number' => $mobileNumber];

        return $this;
    }

    /**
     * @param bool $autoConfirm
     * @return $this
     */
    public function autoConfirm($autoConfirm = true)
    {
        $this->autoConfirm = $autoConfirm;

        return $this;
    }

    /**
     * @param bool $sync
     * @return $this
     */
    public function sync($sync = true)
    {
        $this->sync = $sync;

        return $this;
    }

    /** @return string|null */
    public function getExternalId() { return $this->externalId; }

    /** @return int */
    public function getProductId() { return $this->productId; }

    /** @return array */
    public function getCreditPartyIdentifier() { return $this->creditPartyIdentifier; }

    /** @return bool */
    public function getAutoConfirm() { return $this->autoConfirm; }

    /** @return bool */
    public function isSync() { return $this->sync; }
}
