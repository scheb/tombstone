<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerResult
{
    /**
     * @var AnalyzerFileResult[]
     */
    private $perFile = array();

    /**
     * @var Tombstone[]
     */
    private $dead = array();

    /**
     * @var Tombstone[]
     */
    private $undead = array();

    /**
     * @var Vampire[]
     */
    private $deleted = array();

    /**
     * @param Tombstone $tombstone
     */
    public function addDead(Tombstone $tombstone)
    {
        $this->dead[] = $tombstone;
        $this->initFileIndex($tombstone->getFile());
        $this->perFile[$tombstone->getFile()]->addDead($tombstone);
    }

    /**
     * @param Tombstone $tombstone
     */
    public function addUndead(Tombstone $tombstone)
    {
        $this->undead[] = $tombstone;
        $this->initFileIndex($tombstone->getFile());
        $this->perFile[$tombstone->getFile()]->addUndead($tombstone);
    }

    /**
     * @param Vampire[] $deleted
     */
    public function setDeleted(array $deleted)
    {
        $this->deleted = $deleted;
        foreach ($deleted as $vampire) {
            $this->initFileIndex($vampire->getFile());
            $this->perFile[$vampire->getFile()]->addDeleted($vampire);
        }
    }

    /**
     * @param string $file
     */
    private function initFileIndex($file)
    {
        if (!isset($this->perFile[$file])) {
            $this->perFile[$file] = new AnalyzerFileResult($file);
        }
    }

    /**
     * @return \Scheb\Tombstone\Tombstone[]
     */
    public function getDead()
    {
        return $this->dead;
    }

    /**
     * @return \Scheb\Tombstone\Tombstone[]
     */
    public function getUndead()
    {
        return $this->undead;
    }

    /**
     * @return \Scheb\Tombstone\Vampire[]
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return AnalyzerFileResult[]
     */
    public function getPerFile()
    {
        ksort($this->perFile);
        return $this->perFile;
    }
}
