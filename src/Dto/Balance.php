<?php

namespace Ghanem\Dtone\Dto;

class Balance implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var float */
    private $amount;
    /** @var string */
    private $currency;
    /** @var array */
    private $attributes;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->amount = $data['amount'] ?? 0;
        $dto->currency = $data['currency'] ?? null;
        $dto->attributes = $data;

        return $dto;
    }

    /** @return float */
    public function getAmount() { return $this->amount; }

    /** @return string */
    public function getCurrency() { return $this->currency; }

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
