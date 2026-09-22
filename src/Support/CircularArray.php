<?php

declare(strict_types=1);

namespace CocktailBar\Support;

use InvalidArgumentException;

final class CircularArray
{

    /**
     * @var array<int|null>
     */
    private array $array;

    public function __construct(
        private readonly int $size
    )
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Size must be greater than 0');
        }

        $this->array = array_fill(0, $this->size, null);
    }

    public function get(int $index): int|null
    {
        return $this->array[$this->normalizeIndex($index)];
    }

    /**
     * @return array<int|null>
     */
    public function getAll(): array
    {
        return $this->array;
    }

    public function set(int $index, int|null $value): void
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
