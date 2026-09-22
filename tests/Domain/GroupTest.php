<?php
declare(strict_types=1);

namespace CocktailBar\Tests\Domain;

use CocktailBar\Domain\Group;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GroupTest extends TestCase
{
    public function testGroupCanBeCreatedWithPositiveSize(): void
    {
        $group = new Group(1, 3);

        $this->assertSame(1, $group->id);
        $this->assertSame(3, $group->size);
    }

    public function testGroupSizeCannotBeZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Group(1, 0);
    }

    public function testGroupSizeCannotBeNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Group(2, -1);
    }
}
