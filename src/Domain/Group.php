<?php

declare(strict_types=1);

namespace CocktailBar\Domain;

final readonly class Group
{
    public function __construct(
        public int $id,
        public int $size
    )
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Group size must be greater than zero');
        }
    }
}