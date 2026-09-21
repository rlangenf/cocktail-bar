<?php

namespace CocktailBar\Tests\Domain;

use CocktailBar\Domain\Bar;
use PHPUnit\Framework\TestCase;

class BarTest extends TestCase
{
    public function testBarCapacity()
    {
        $bar = new Bar(10);
        $this->assertEquals(10, $bar->getCapacity());

        $this->expectException(\InvalidArgumentException::class);
        $bar = new Bar(0);

        $this->expectException(\InvalidArgumentException::class);
        $bar = new Bar(-1);
    }

    public function testSeats()
    {
        $bar = new Bar(10);
        $this->assertEquals(array_fill(0, 10, null), $bar->getSeats());
    }
}