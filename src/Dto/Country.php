<?php

namespace Ghanem\Dtone\Dto;

class Country
{
    /** @var string */
    private $isoCode;
    /** @var string */
    private $name;
    /** @var array */
    private $regions;

    private function __construct() {}

    /** @return static */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->isoCode = $data['iso_code'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->regions = $data['regions'] ?? [];

        return $dto;
    }

    /** @return string */
    public function getIsoCode() { return $this->isoCode; }

    /** @return string */
    public function getName() { return $this->name; }

    /** @return array */
    public function getRegions() { return $this->regions; }

    /** @return array */
    public function toArray()
    {
        return [
            'iso_code' => $this->isoCode,
            'name' => $this->name,
            'regions' => $this->regions,
        ];
    }
}
