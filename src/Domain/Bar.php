<?php

declare(strict_types=1);

namespace CocktailBar\Domain;

use CocktailBar\Support\CircularArray;
use InvalidArgumentException;

final class Bar
{
    private readonly int $capacity;

    private CircularArray $seats;

    public function __construct(int $capacity)
    {
        if ($capacity <= 0) {
            throw new InvalidArgumentException('Capacity must be greater than 0');
        }

        $this->capacity = $capacity;

        $this->seats = new CircularArray($capacity);
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * @return array<int|null>
     */
    public function getSeats(): array
    {
        return $this->seats->getAll();
    }

    /**
     * This function attempts to seat a group at the bar. It returns true if the group was successfully seated, false otherwise.
     *
     * @param Group $group
     * @return bool
     */
    public function seat(Group $group): bool
    {
        if ($group->size > $this->capacity || !empty($this->seatingFor($group->id))) {
            return false;
        }

        $seatGroup = $this->findBestMatchingSeatGroup($group->size);

        if ($seatGroup === null) {
            return false;
        }

        $this->seatGroupAt($seatGroup['index'], $group);

        return true;
    }

    /**
     * This function attempts to find the best matching empty seat group for a given group size.
     *
     * @param int $groupSize
     * @return null|array{index: int, size: int}
     */
    private function findBestMatchingSeatGroup(int $groupSize): ?array
    {
        $bestMatch = null;

        foreach ($this->getEmptySeatGroups() as $seatGroup) {
            if ($seatGroup['size'] < $groupSize) {
                continue;
            }

            if ($bestMatch === null || $seatGroup['size'] < $bestMatch['size']) {
                $bestMatch = $seatGroup;
            }
        }

        return $bestMatch;
    }

    /**
     * This function checks the whole bar for adjacent empty seats and returns indexes and sizes of the groups in an array.
     *
     * @return array{index: int, size: int}[]
     */
    private function getEmptySeatGroups(): array
    {
        $groups = [];
        $currentGroup = null;

        for ($i = 0; $i < $this->seats->size(); $i++) {
            if ($this->seats->get($i) !== null) {
                if ($currentGroup !== null) {
                    $groups[] = $currentGroup;
                    $currentGroup = null;
                }

                continue;
            }

            $currentGroup ??= [
                'index' => $i,
                'size' => 0,
            ];

            $currentGroup['size']++;
        }

        if ($currentGroup !== null) {
            $groups[] = $currentGroup;
        }

        return $this->mergeCircularGroups($groups);
    }

    /**
     * If there is more than one group, this function checks if the first and the last group are circular.
     * If so, merge them to form a single group.
     *
     * @param array{index: int, size: int}[] $groups
     * @return array{index: int, size: int}[]
     */
    private function mergeCircularGroups(array $groups): array
    {
        if (count($groups) < 2) {
            return $groups;
        }

        $first = $groups[0];
        $lastIndex = count($groups) - 1;
        $last = $groups[$lastIndex];

        if (
            $first['index'] !== 0 ||
            $last['index'] + $last['size'] !== $this->seats->size()
        ) {
            return $groups;
        }

        $groups[$lastIndex]['size'] += $first['size'];

        array_shift($groups);

        return $groups;
    }

    /**
     * Seats a group at a given index.
     *
     * @param int $index The index at which to seat the group.
     * @param Group $group The group to seat.
     */
    private function seatGroupAt(int $index, Group $group): void
    {
        for ($i = 0; $i < $group->size; $i++) {
            $this->seats->set($index + $i, $group->id);
        }
    }

    /**
     * Removes a group from the bar.
     *
     * @param int $groupId The ID of the group to remove.
     */
    public function leave(int $groupId): void
    {
        foreach ($this->seatingFor($groupId) as $seatNumber) {
            $this->seats->set($seatNumber, null);
        }
    }

    /**
     * Returns the seating keys for a given group ID.
     *
     * @param int $groupId The ID of the group to retrieve the seating arrangement for.
     * @return int[] An array of keys (i.e. seat numbers) where the group is seated.
     */
    private function seatingFor(int $groupId): array
    {
        return array_keys($this->seats->getAll(), $groupId, true);
    }
}
