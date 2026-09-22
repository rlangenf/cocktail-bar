<?php
declare(strict_types=1);

namespace CocktailBar\Tests\Command;

use CocktailBar\Application\LoungeService;
use CocktailBar\Command\LoungeCommand;
use CocktailBar\Domain\Bar;
use PHPUnit\Framework\TestCase;

class LoungeCommandTest extends TestCase
{
    public function testInputHandle()
    {
        $cmd = new LoungeCommand(new LoungeService(new Bar(6)));

        $this->assertSame("Group 1 seated\n", $cmd->handle('enter 2'));
        $this->assertSame("Group 1 left\n", $cmd->handle('leave 1'));
        $this->assertSame("Group 2 seated\n", $cmd->handle('enter     2'));
    }

    public function testFaultyInputHandle()
    {
        $cmd = new LoungeCommand(new LoungeService(new Bar(4)));

        $this->assertStringContainsString('Failed to seat group', $cmd->handle('enter 5'));
        $this->assertStringContainsString('Enter command requires a valid group size', $cmd->handle('enter abc'));
        $this->assertStringContainsString('Group size must be greater than 0', $cmd->handle('enter 0'));
        $this->assertStringContainsString('Leave command requires a valid group ID', $cmd->handle('leave'));
        $this->assertStringContainsString('Leave command requires a valid group ID', $cmd->handle('leave abc'));
        $this->assertStringContainsString('Group 99 not found', $cmd->handle('leave 99'));
    }

    public function testHelp()
    {
        $cmd = new LoungeCommand(new LoungeService(new Bar(6)));

        $help = $cmd->handle('help');

        $this->assertStringContainsString('enter <size>', $help);
        $this->assertStringContainsString('leave <groupId>', $help);
        $this->assertStringContainsString('status', $help);
        $this->assertStringContainsString('help', $help);
        $this->assertStringContainsString('exit', $help);
    }

    public function testUnknownCommand()
    {
        $cmd = new LoungeCommand(new LoungeService(new Bar(6)));
        $this->assertStringContainsString('Unknown command', $cmd->handle('foo'));
    }

    public function testStatus()
    {
        $cmd = new LoungeCommand(new LoungeService(new Bar(6)));

        $cmd->handle('enter 2');
        $status = $cmd->handle('status');

        $this->assertStringContainsString('-> [0: 1] [1: 1] [2: _] [3: _] [4: _] [5: _] ->', $status);
    }
}