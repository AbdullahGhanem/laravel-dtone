<?php

namespace Ghanem\Dtone\Dto;

class Operator implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var Country|null */
    private $country;
    /** @var array */
    private $regions;
    /** @var array */
    private $attributes;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->country = isset($data['country']) ? Country::fromArray($data['country']) : null;
        $dto->regions = $data['regions'] ?? [];
        $dto->attributes = $data;

        return $dto;
    }

    /** @return int */
    public function getId() { return $this->id; }

    /** @return string */
    public function getName() { return $this->name; }

    /** @return Country|null */
    public function getCountry() { return $this->country; }

    /** @return array */
    public function getRegions() { return $this->regions; }

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
