<?php

namespace CocktailBar\Support;

final class CircularArray
{

    private array $array;

    public function __construct(
        private readonly int $size
    )
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Size must be greater than 0');
        }

        $this->array = array_fill(0, $this->size, null);
    }

    public function get(int $index): mixed
    {
        return $this->array[$this->normalizeIndex($index)];
    }

    public function getAll(): array
    {
        return $this->array;
    }

    public function set(int $index, mixed $value): void
    {
        $this->array[$this->normalizeIndex($index)] = $value;
    }

    public function size(): int
    {
        return $this->size;
    }

    private function normalizeIndex(int $index): int
    {
        return ($index % $this->size + $this->size) % $this->size;
    }
}