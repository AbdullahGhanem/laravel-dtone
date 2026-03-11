<?php

namespace Ghanem\Dtone\Dto;

class Product implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string|null */
    private $description;
    /** @var string|null */
    private $type;
    /** @var Service|null */
    private $service;
    /** @var Operator|null */
    private $operator;
    /** @var array */
    private $attributes;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->type = $data['type'] ?? null;
        $dto->service = isset($data['service']) ? Service::fromArray($data['service']) : null;
        $dto->operator = isset($data['operator']) ? Operator::fromArray($data['operator']) : null;
        $dto->attributes = $data;

        return $dto;
    }

    /** @return int */
    public function getId() { return $this->id; }

    /** @return string */
    public function getName() { return $this->name; }

    /** @return string|null */
    public function getDescription() { return $this->description; }

    /** @return string|null */
    public function getType() { return $this->type; }

    /** @return Service|null */
    public function getService() { return $this->service; }

    /** @return Operator|null */
    public function getOperator() { return $this->operator; }

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
