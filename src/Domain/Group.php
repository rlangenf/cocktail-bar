<?php

namespace CocktailBar\Domain;

final readonly class Group
{
    public function __construct(
        public readonly int $id,
        public readonly int $size
    )
    {
    }
}