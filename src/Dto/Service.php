<?php

namespace Ghanem\Dtone\Dto;

class Service implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var int */
    private $id;
    /** @var string */
    private $name;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;

        return $dto;
    }

    /** @return int */
    public function getId() { return $this->id; }

    /** @return string */
    public function getName() { return $this->name; }

    /** @return array */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
