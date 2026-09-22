<?php
declare(strict_types=1);

namespace CocktailBar\Application;

use CocktailBar\Domain\Bar;
use CocktailBar\Domain\Group;

final class LoungeService
{

    /**
     * @var array<int, Group>
     */
    private array $groups = [];

    private int $nextGroupId = 1;

    public function __construct(
        private readonly Bar $bar
    )
    {
    }

    /**
     * @param int $groupSize
     * @return int|null
     */
    public function enter(int $groupSize): ?int
    {
        $group = new Group($this->nextGroupId, $groupSize);

        if ($this->bar->seat($group)) {
            $this->groups[$group->id] = $group;

            $this->nextGroupId++;

            return $group->id;
        }

        return null;
    }

    /**
     * @param int $groupId
     * @return bool
     */
    public function leave(int $groupId): bool
    {
        if (!array_key_exists($groupId, $this->groups)) {
            return false;
        }

        $this->bar->leave($groupId);

        unset($this->groups[$groupId]);

        return true;
    }

    /**
     * @return array<int, int>
     */
    public function seats(): array
    {
        return $this->bar->getSeats();
    }

    /**
     * @return array<int, Group>
     */
    public function groups(): array
    {
        return $this->groups;
    }
}