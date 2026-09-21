<?php

namespace CocktailBar\Tests\Domain;

use CocktailBar\Domain\Bar;
use CocktailBar\Domain\Group;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BarTest extends TestCase
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

    public function testEmptyBarAcceptsGroup()
    {
        $bar = new Bar(10);
        $group = new Group(1, 5);
        $this->assertTrue($bar->seat($group));
    }

    public function testBarDeclinesGroup()
    {
        $bar = new Bar(1);
        $group = new Group(1, 5);
        $this->assertFalse($bar->seat($group));
    }

    public function testGroupCanLeave()
    {
        $bar = new Bar(10);
        $group = new Group(1, 5);
        $bar->seat($group);
        $bar->leave($group->id);
        $this->assertEquals(array_fill(0, 10, null), $bar->getSeats());
    }

    public function testGroupIsSeated()
    {
        $bar = new Bar(10);
        $group = new Group(1, 5);
        $bar->seat($group);

        $this->assertEquals(array(1, 1, 1, 1, 1, null, null, null, null, null), $bar->getSeats());
    }

    public function testTwoGroupsCanSeat()
    {
        $bar = new Bar(10);
        $firstGroup = new Group(1, 5);
        $bar->seat($firstGroup);

        $secondGroup = new Group(2, 5);
        $bar->seat($secondGroup);

        $this->assertEquals(array(1, 1, 1, 1, 1, 2, 2, 2, 2, 2), $bar->getSeats());
    }

    public function testGroupSeatingWrapsAround()
    {
        $bar = new Bar(10);

        $firstGroup = new Group(1, 2);
        $bar->seat($firstGroup);

        $secondGroup = new Group(2, 7);
        $bar->seat($secondGroup);

        $bar->leave($firstGroup->id);

        $thirdGroup = new Group(3, 3);
        $bar->seat($thirdGroup);

        $this->assertEquals(array(3, 3, 2, 2, 2, 2, 2, 2, 2, 3), $bar->getSeats());
    }

    public function testBestMatchSeating()
    {
        $bar = new Bar(10);

        $firstGroup = new Group(1, 1);
        $bar->seat($firstGroup);

        $secondGroup = new Group(2, 5);
        $bar->seat($secondGroup);

        $thirdGroup = new Group(3, 2);
        $bar->seat($thirdGroup);

        $fourthGroup = new Group(4, 2);
        $bar->seat($fourthGroup);

        // make the second and last group leave
        $bar->leave($secondGroup->id);
        $bar->leave($fourthGroup->id);

        // make them join the other way around
        $bar->seat($fourthGroup);
        $bar->seat($secondGroup);

        $this->assertEquals(array(1, 2, 2, 2, 2, 2, 3, 3, 4, 4), $bar->getSeats());
    }
}