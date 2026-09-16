<?php

namespace CocktailBar\Domain;

final class Bar
{
    private readonly int $capacity;

    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }
}