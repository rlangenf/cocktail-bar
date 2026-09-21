<?php

namespace CocktailBar\Domain;

use CocktailBar\Support\CircularArray;

final class Bar
{
    private readonly int $capacity;

    private CircularArray $seats;

    public function __construct(int $capacity)
    {
        if ($capacity <= 0) {
            throw new \InvalidArgumentException('Capacity must be greater than 0');
        }

        $this->capacity = $capacity;

        $this->seats = new CircularArray($capacity);
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getSeats(): array
    {
        return $this->seats->getAll();
    }

    public function seat(Group $group): bool
    {
        if ($group->size > $this->capacity) {
            return false;
        }

        $emptySeats = array_keys($this->seats->getAll(), null);

        // seat number from which the group can start sitting
        $groupStartIndex = null;

        foreach ($emptySeats as $seatNumber) {
            $groupStartIndex = $seatNumber;

            // check if next seat number is available
            for ($i = 1; $i < $group->size; $i++) {

                // if the next seat is occupied, go to next empty seat
                if ($this->seats->get($seatNumber + $i) != null) {
                    $groupStartIndex = null;
                    break;
                }
            }

            if ($groupStartIndex !== null) {
                break;
            }
        }

        if ($groupStartIndex === null) {
            return false;
        }

        for ($i = 0; $i < $group->size; $i++) {
            $this->seats->set($groupStartIndex + $i, $group->id);
        }

        return true;
    }

    public function leave(int $groupId): void
    {
        foreach ($this->seatingFor($groupId) as $seatNumber) {
            $this->seats->set($seatNumber, null);
        }
    }

    public function isSeatEmpty($seatNumber): bool
    {
        return $this->seats->get($seatNumber) === null;
    }

    public function seatingFor(int $groupId): array
    {
        return array_keys($this->seats->getAll(), $groupId);
    }
}