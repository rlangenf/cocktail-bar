<?php
declare(strict_types=1);

namespace CocktailBar\Tests\Application;

use CocktailBar\Application\LoungeService;
use CocktailBar\Domain\Bar;
use PHPUnit\Framework\TestCase;

class LoungeServiceTest extends TestCase
{
    public function testLoungeServiceEnter()
    {
        $bar = new Bar(10);
        $loungeService = new LoungeService($bar);

        $groupId = $loungeService->enter(5);

        $this->assertSame(1, $groupId);
    }

    public function testLoungeServiceLeave()
    {
        $bar = new Bar(10);
        $loungeService = new LoungeService($bar);

        $groupId = $loungeService->enter(5);

        $this->assertTrue($loungeService->leave($groupId));
        $this->assertFalse($loungeService->leave($groupId));
    }

    public function testLoungeServiceDenySeats()
    {
        $bar = new Bar(5);
        $loungeService = new LoungeService($bar);

        $this->assertSame(1, $loungeService->enter(3));
        $this->assertNull($loungeService->enter(3));
        $this->assertSame(2, $loungeService->enter(2));
    }
}