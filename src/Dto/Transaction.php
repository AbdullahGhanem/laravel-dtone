<?php

namespace Ghanem\Dtone\Dto;

class Transaction
{
    /** @var int */
    private $id;
    /** @var string|null */
    private $externalId;
    /** @var string|null */
    private $status;
    /** @var array */
    private $attributes;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->externalId = $data['external_id'] ?? null;
        $dto->status = $data['status'] ?? null;
        $dto->attributes = $data;

        return $dto;
    }

    /** @return int */
    public function getId() { return $this->id; }

    /** @return string|null */
    public function getExternalId() { return $this->externalId; }

    /** @return string|null */
    public function getStatus() { return $this->status; }

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /** @return array */
    public function toArray()
    {
        return $this->attributes;
    }
}
