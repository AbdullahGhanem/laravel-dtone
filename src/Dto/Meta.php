<?php

namespace Ghanem\Dtone\Dto;

class Meta
{
    /** @var int|null */
    private $total;
    /** @var int|null */
    private $totalPages;
    /** @var int|null */
    private $perPage;
    /** @var int|null */
    private $page;
    /** @var int|null */
    private $nextPage;
    /** @var int|null */
    private $prevPage;

    private function __construct() {}

    /**
     * @return static
     */
    public static function fromArray(array $data)
    {
        $dto = new self();
        $dto->total = isset($data['total']) ? (int) $data['total'] : null;
        $dto->totalPages = isset($data['total_pages']) ? (int) $data['total_pages'] : null;
        $dto->perPage = isset($data['per_page']) ? (int) $data['per_page'] : null;
        $dto->page = isset($data['page']) ? (int) $data['page'] : null;
        $dto->nextPage = isset($data['next_page']) ? (int) $data['next_page'] : null;
        $dto->prevPage = isset($data['prev_page']) ? (int) $data['prev_page'] : null;

        return $dto;
    }

    /** @return int|null */
    public function getTotal() { return $this->total; }

    /** @return int|null */
    public function getTotalPages() { return $this->totalPages; }

    /** @return int|null */
    public function getPerPage() { return $this->perPage; }

    /** @return int|null */
    public function getPage() { return $this->page; }

    /** @return int|null */
    public function getNextPage() { return $this->nextPage; }

    /** @return int|null */
    public function getPrevPage() { return $this->prevPage; }

    /** @return array */
    public function toArray()
    {
        return [
            'total' => $this->total,
            'total_pages' => $this->totalPages,
            'per_page' => $this->perPage,
            'page' => $this->page,
            'next_page' => $this->nextPage,
            'prev_page' => $this->prevPage,
        ];
    }
}
