<?php
declare(strict_types=1);

namespace CocktailBar\Command;

use CocktailBar\Application\LoungeService;

final readonly class LoungeCommand
{

    private const int COLUMN_WIDTH = 10;
    private const int SEATS_PER_LINE = 10;

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
            enter <size> - tries to seat a group at the bar with the size <size>
            leave <groupId> - removes the group with id <groupId>
            status - print the current bar seating 
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

    /**
     * @param array<int, string> $parts
     * @return string
     */
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

    /**
     * @param array<int, string> $parts
     * @return string
     */
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

    private function errorMessage(string $message): string
    {
        return "Error: $message\n";
    }

    private function handleStatus(): string
    {

        $out = "\nCurrent seating:\n";
        $out .= "-> ";

        $seats = $this->loungeService->seats();

        $seatCount = 0;
        foreach ($seats as $seatNumber => $seat) {
            $out .= $this->seatCell($seatNumber, $seat);

            if ($seatNumber === count($seats) - 1) {
                $out .= "->";
            }

            $seatCount++;
            if ($seatCount % self::SEATS_PER_LINE === 0) {
                $out .= "\n";
            }
        }

        $out .= "\nSeated groups:\n";
        $out .= $this->tableRow('Group-ID', 'Size');

        foreach ($this->loungeService->groups() as $group) {
            $out .= $this->tableRow((string)$group->id, (string)$group->size);
        }

        if (!empty($this->loungeService->rejectedGroups())) {
            $out .= "\nRejected groups:\n";
            $out .= $this->tableRow('Group-ID', 'Size');

            foreach ($this->loungeService->rejectedGroups() as $group) {
                $out .= $this->tableRow((string)$group->id, (string)$group->size);
            }
        }

        return $out;
    }

    private function seatCell(int $seatNumber, ?int $groupId): string
    {
        return '[' . $seatNumber . ': ' . ($groupId ?? '_') . '] ';
    }

    /**
     * Renders a single right-aligned table row.
     */
    private function tableRow(string $groupId, string $size): string
    {
        return str_pad($groupId, self::COLUMN_WIDTH, ' ', STR_PAD_LEFT)
            . ' | '
            . str_pad($size, self::COLUMN_WIDTH, ' ', STR_PAD_LEFT)
            . "\n";
    }
}
