<?php

namespace Ghanem\Dtone\Dto;

class Promotion
{
    /** @var int */
    private $id;
    /** @var string|null */
    private $name;
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
        $dto->operator = isset($data['operator']) ? Operator::fromArray($data['operator']) : null;
        $dto->attributes = $data;

        return $dto;
    }

    /** @return int */
    public function getId() { return $this->id; }

    /** @return string|null */
    public function getName() { return $this->name; }

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
