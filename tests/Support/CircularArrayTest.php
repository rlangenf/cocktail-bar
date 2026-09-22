<?php
declare(strict_types=1);

namespace CocktailBar\Tests\Support;

use CocktailBar\Support\CircularArray;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CircularArrayTest extends TestCase
{
    public function testArrayHasConfiguredSize(): void
    {
        $array = new CircularArray(10);

        $this->assertSame(10, $array->size());
    }

    public function testArrayCannotHaveZeroSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CircularArray(0);
    }

    public function testArrayCannotHaveNegativeSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CircularArray(-1);
    }

    public function testNewElementsAreNull(): void
    {
        $array = new CircularArray(3);

        $this->assertNull($array->get(0));
        $this->assertNull($array->get(1));
        $this->assertNull($array->get(2));
    }

    public function testValueCanBeSetAndRetrieved(): void
    {
        $array = new CircularArray(3);

        $array->set(1, 1);

        $this->assertSame(1, $array->get(1));
    }

    public function testIndexWrapsAroundAtEnd(): void
    {
        $array = new CircularArray(3);

        $array->set(0, 1);

        $this->assertSame(1, $array->get(3));
        $this->assertSame(1, $array->get(6));
    }

    public function testNegativeIndexWrapsAroundAtBeginning(): void
    {
        $array = new CircularArray(3);

        $array->set(2, 1);

        $this->assertSame(1, $array->get(-1));
        $this->assertSame(1, $array->get(-4));
    }
}
