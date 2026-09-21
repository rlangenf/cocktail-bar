<?php

namespace CocktailBar\Tests\Domain;

use CocktailBar\Domain\Bar;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BarTest extends TestCase
{
    public function testBarCapacity()
    {
        $bar = new Bar(10);
        $this->assertEquals(10, $bar->getCapacity());
    }

    public function testBarCapacityCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        new Bar(0);
    }

    public function testBarCapacityCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        new Bar(-1);
    }

    public function testSeats()
    {
        $bar = new Bar(10);
        $this->assertEquals(array_fill(0, 10, null), $bar->getSeats());
    }
}