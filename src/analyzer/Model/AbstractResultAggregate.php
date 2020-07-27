<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

abstract class AbstractResultAggregate implements ResultAggregateInterface
{
    /**
     * @var Tombstone[]
     */
    protected $dead = [];

    /**
     * @var Tombstone[]
     */
    protected $undead = [];

    /**
     * @var Vampire[]
     */
    protected $deleted = [];

    /**
     * @param Tombstone[] $dead
     * @param Tombstone[] $undead
     * @param Vampire[] $deleted
     */
    public function __construct(array $dead, array $undead, array $deleted)
    {
        $this->dead = $dead;
        $this->undead = $undead;
        $this->deleted = $deleted;
    }

    /**
     * @return Tombstone[]
     */
    public function getDead(): array
    {
        return $this->dead;
    }

    /**
     * @return Tombstone[]
     */
    public function getUndead(): array
    {
        return $this->undead;
    }

    /**
     * @return Vampire[]
     */
    public function getDeleted(): array
    {
        return $this->deleted;
    }

    public function getDeadCount(): int
    {
        return \count($this->dead);
    }

    public function getUndeadCount(): int
    {
        return \count($this->undead);
    }

    public function getDeletedCount(): int
    {
        return \count($this->deleted);
    }
}
