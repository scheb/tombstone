<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerFileResult
{
    /**
     * @var string
     */
    private $file;

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
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param Tombstone $tombstone
     */
    public function addDead(Tombstone $tombstone)
    {
        $this->dead[] = $tombstone;
    }

    /**
     * @param Tombstone $tombstone
     */
    public function addUndead(Tombstone $tombstone)
    {
        $this->undead[] = $tombstone;
    }

    /**
     * @param Vampire $vampire
     */
    public function addDeleted(Vampire $vampire) {
        $this->deleted[] = $vampire;
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
     * @return int
     */
    public function getDeadCount()
    {
        return count($this->dead);
    }

    /**
     * @return int
     */
    public function getUndeadCount()
    {
        return count($this->undead);
    }

    /**
     * @return int
     */
    public function getDeletedCount()
    {
        return count($this->deleted);
    }
}
