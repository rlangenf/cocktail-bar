<?php

namespace CocktailBar\Domain;

final class Bar
{
    private readonly int $capacity;

    private array $seats = [];

    public function __construct(int $capacity)
    {
        if ($capacity <= 0) {
            throw new \InvalidArgumentException('Capacity must be greater than 0');
        }

        $this->capacity = $capacity;

        for ($i = 0; $i < $this->capacity; $i++) {
            $this->seats[] = null;
        }
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }

    public function seat(Group $group): void
    {

    }

    public function leave(int $groupId): void
    {

    }

    public function isSeatEmpty($seatNumber): bool
    {
        return $this->seats[$seatNumber] === null;
    }

    public function seatingFor(int $groupId): array
    {
        return array_keys($this->seats, $groupId);
    }
}