<?php

use CocktailBar\Domain\Bar;

class BarTest extends \PHPUnit\Framework\TestCase
{
    public function testBarCapacity()
    {
        $bar = new Bar(10);
        $this->assertEquals(10, $bar->getCapacity());
    }
}