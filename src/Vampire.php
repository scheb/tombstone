<?php
namespace Scheb\Tombstone;

class Vampire
{
    /**
     * @var string
     */
    private $awakeningDate;

    /**
     * @var string|null
     */
    private $invoker;

    /**
     * @var Tombstone
     */
    private $tombstone;

    /**
     * @param string $awakeningDate
     * @param string|null $invoker
     * @param Tombstone $tombstone
     */
    public function __construct($awakeningDate, $invoker, Tombstone $tombstone)
    {
        $this->awakeningDate = $awakeningDate;
        $this->invoker = $invoker;
        $this->tombstone = $tombstone;
    }

    /**
     * @param string $date
     * @param string $author
     * @param array $trace
     * @return Vampire
     */
    public static function createFromCall($date, $author, $trace)
    {
        $firstFrame = $trace[0];
        $secondFrame = isset($trace[1]) ? $trace[1] : null;
        $file = $firstFrame['file'];
        $line = $firstFrame['line'];
        $method = self::getMethodFromTrace($firstFrame);
        $invoker = $secondFrame ? self::getMethodFromTrace($secondFrame) : null;
        $tombstone = new Tombstone($date, $author, $file, $line, $method);

        return new self(date('c'), $invoker, $tombstone);
    }

    /**
     * @param array $frame
     * @return string
     */
    private static function getMethodFromTrace($frame)
    {
        return (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
    }

    /**
     * @return string
     */
    public function getAwakeningDate()
    {
        return $this->awakeningDate;
    }

    /**
     * @return null|string
     */
    public function getInvoker()
    {
        return $this->invoker;
    }

    /**
     * @return Tombstone
     */
    public function getTombstone()
    {
        return $this->tombstone;
    }

    /**
     * @param Tombstone $tombstone
     */
    public function setTombstone($tombstone)
    {
        $this->tombstone = $tombstone;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->tombstone->getAuthor();
    }

    /**
     * @return string
     */
    public function getTombstoneDate()
    {
        return $this->tombstone->getTombstoneDate();
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->tombstone->getPosition();
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->tombstone->getFile();
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->tombstone->getLine();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->tombstone->getMethod();
    }
}
