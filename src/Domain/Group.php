<?php

namespace CocktailBar\Domain;

final readonly class Group
{
    public function __construct(
        public readonly int $id,
        public readonly int $size
    )
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Group size must be greater than zero');
        }
    }
}