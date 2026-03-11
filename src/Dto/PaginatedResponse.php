<?php

namespace Ghanem\Dtone\Dto;

class PaginatedResponse implements \ArrayAccess, \JsonSerializable
{
    use Concerns\ArrayAccessible;

    /** @var array */
    private $data;
    /** @var Meta */
    private $meta;

    private function __construct() {}

    /**
     * @param array  $result   Raw response with 'data' and 'meta' keys
     * @param string $dtoClass The DTO class to hydrate each item with
     * @return static
     */
    public static function fromArray(array $result, $dtoClass)
    {
        $dto = new self();
        $dto->data = array_map([$dtoClass, 'fromArray'], $result['data'] ?? []);
        $dto->meta = Meta::fromArray($result['meta'] ?? []);

        return $dto;
    }

    /** @return array */
    public function getData() { return $this->data; }

    /** @return Meta */
    public function getMeta() { return $this->meta; }

    /** @return array */
    public function toArray()
    {
        return [
            'data' => array_map(function ($item) {
                return method_exists($item, 'toArray') ? $item->toArray() : $item;
            }, $this->data),
            'meta' => $this->meta->toArray(),
        ];
    }
}
