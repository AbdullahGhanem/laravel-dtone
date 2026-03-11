<?php

namespace Ghanem\Dtone\Dto;

class BenefitType implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var string */
    private $name;
    /** @var array */
    private $attributes;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->name = $data['name'] ?? null;
        $dto->attributes = $data;

        return $dto;
    }

    /** @return string */
    public function getName() { return $this->name; }

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
