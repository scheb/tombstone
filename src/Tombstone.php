<?php
namespace Scheb\Tombstone;

class Tombstone
{
    /**
     * @var string
     */
    private $tombstoneDate;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string
     */
    private $method;

    /**
     * @var Vampire[]
     */
    private $vampires = array();

    /**
     * @param string $tombstoneDate
     * @param string $author
     * @param string $label
     * @param string $file
     * @param int $line
     * @param string $method
     */
    public function __construct($tombstoneDate, $author, $label, $file, $line, $method)
    {
        $this->tombstoneDate = $tombstoneDate;
        $this->author = $author;
        $this->file = $file;
        $this->line = $line;
        $this->method = $method;
        $this->label = $label;
    }

    /**
     * @param string $file
     * @param string $line
     * @return string
     */
    public static function createPosition($file, $line)
    {
        return $file . ':' . $line;
    }

    /**
     * @param Tombstone $tombstone
     *
     * @return bool
     */
    public function inscriptionEquals(Tombstone $tombstone)
    {
        return $tombstone->getAuthor() === $this->author && $tombstone->getTombstoneDate() == $this->tombstoneDate;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getTombstoneDate()
    {
        return $this->tombstoneDate;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return self::createPosition($this->file, $this->line);
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param Vampire $vampire
     */
    public function addVampire(Vampire $vampire)
    {
        $this->vampires[] = $vampire;
    }

    /**
     * @return Vampire[]
     */
    public function getVampires()
    {
        return $this->vampires;
    }

    /**
     * @return bool
     */
    public function hasVampires()
    {
        return !$this->vampires;
    }
}
