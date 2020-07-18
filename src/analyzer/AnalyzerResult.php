<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;

class AnalyzerResult
{
    /**
     * @var AnalyzerFileResult[]
     */
    private $perFile = [];

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

    public function addDead(Tombstone $tombstone): void
    {
        $this->dead[] = $tombstone;
        $this->initFileIndex($tombstone->getFile());
        $this->perFile[$tombstone->getFile()]->addDead($tombstone);
    }

    public function addUndead(Tombstone $tombstone): void
    {
        $this->undead[] = $tombstone;
        $this->initFileIndex($tombstone->getFile());
        $this->perFile[$tombstone->getFile()]->addUndead($tombstone);
    }

    /**
     * @param Vampire[] $deleted
     */
    public function setDeleted(array $deleted): void
    {
        $this->deleted = $deleted;
        foreach ($deleted as $vampire) {
            $this->initFileIndex($vampire->getFile());
            $this->perFile[$vampire->getFile()]->addDeleted($vampire);
        }
    }

    private function initFileIndex(string $file): void
    {
        if (!isset($this->perFile[$file])) {
            $this->perFile[$file] = new AnalyzerFileResult($file);
        }
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

    /**
     * @return AnalyzerFileResult[]
     */
    public function getPerFile(): array
    {
        ksort($this->perFile);

        return $this->perFile;
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
