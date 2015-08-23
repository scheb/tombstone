<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerResult
{

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
    }

    /**
     * @param Tombstone $tombstone
     */
    public function addUndead(Tombstone $tombstone)
    {
        $this->undead[] = $tombstone;
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
     * @param array $deleted
     */
    public function setDeleted(array $deleted)
    {
        $this->deleted = $deleted;
    }
}
