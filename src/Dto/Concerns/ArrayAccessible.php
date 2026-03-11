<?php

namespace Ghanem\Dtone\Dto\Concerns;

trait ArrayAccessible
{
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // DTOs are immutable
    }

    public function offsetUnset(mixed $offset): void
    {
        // DTOs are immutable
    }
}
