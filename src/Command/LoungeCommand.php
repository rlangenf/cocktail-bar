<?php
declare(strict_types=1);

namespace CocktailBar\Command;

use CocktailBar\Application\LoungeService;

final readonly class LoungeCommand
{
    public function __construct(
        private LoungeService $loungeService
    )
    {
    }

    public function run(): void
    {
        echo $this->printHelp();

        while (true) {
            $input = readline('> ');

            if ($input === false) {
                break;
            }

            $input = trim($input);

            if ($input === '') {
                continue;
            }

            if ($input === 'exit') {
                break;
            }

            echo $this->handle($input);
        }
    }

    private function printHelp(): string
    {
        return <<<TEXT
            Available commands:
            enter <size> - enter the lounge with a bar of size <size>
            leave <groupId> - leave the lounge with a group of size <groupId>
            status - print the current seating 
            help - print this help message
            exit - exit the application

        TEXT;
    }

    public function handle(string $input): string
    {
        $parts = preg_split('/\s+/', $input);

        $command = $parts[0];

        return match ($command) {
            'enter' => $this->handleEnter($parts),
            'leave' => $this->handleLeave($parts),
            'status' => $this->handleStatus(),
            'help' => $this->printHelp(),
            default => $this->errorMessage("Unknown command: $command"),
        };
    }

    private function handleEnter(array $parts): string
    {
        if (count($parts) !== 2 || !is_numeric($parts[1])) {
            return $this->errorMessage('Enter command requires a valid group size');
        }

        $size = (int)$parts[1];
        if ($size < 1) {
            return $this->errorMessage('Group size must be greater than 0');
        }

        $groupId = $this->loungeService->enter($size);

        if ($groupId === null) {
            return $this->errorMessage('Failed to seat group. Not enough space.');
        }

        return "Group $groupId seated\n";
    }

    private function errorMessage(string $message): string
    {
        return "Error: $message\n";
    }

    private function handleLeave(array $parts): string
    {
        if (count($parts) !== 2 || !is_numeric($parts[1])) {
            return $this->errorMessage('Leave command requires a valid group ID');
        }

        $groupId = (int)$parts[1];

        if ($this->loungeService->leave($groupId)) {
            return "Group $groupId left\n";
        } else {
            return $this->errorMessage("Group $groupId not found");
        }
    }

    private function handleStatus(): string
    {
        return $this->printStatus();
    }

    private function printStatus(): string
    {
        echo "\nCurrent seating:\n";
        print_r($this->loungeService->seats());

        echo "\nSeated groups:\n";
        echo "\nGroup-ID | Size\n";
        foreach ($this->loungeService->groups() as $group) {
            echo "$group->id | $group->size\n";
        }

        return '';
    }
}