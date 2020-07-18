<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;

class AnalyzerFileResult implements ResultAggregateInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var Tombstone[]
     */
    private $dead = [];

    /**
     * @var Tombstone[]
     */
    private $undead = [];

    /**
     * @var Vampire[]
     */
    private $deleted = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function addDead(Tombstone $tombstone): void
    {
        $this->dead[] = $tombstone;
    }

    public function addUndead(Tombstone $tombstone): void
    {
        $this->undead[] = $tombstone;
    }

    public function addDeleted(Vampire $vampire): void
    {
        $this->deleted[] = $vampire;
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
