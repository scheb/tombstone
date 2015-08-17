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
        // This is the call to the tombstone
        $tombstoneCall = $trace[0];
        $file = $tombstoneCall['file'];
        $line = $tombstoneCall['line'];

        // This is the method with the tombstone contained
        $context = isset($trace[1]) ? $trace[1] : null;
        $method = self::getMethodFromTrace($context);

        // This is the method that called the method with the tombstone
        $secondFrame = isset($trace[2]) ? $trace[2] : null;
        $invoker = self::getMethodFromTrace($secondFrame);

        $tombstone = new Tombstone($date, $author, $file, $line, $method);

        return new self(date('c'), $invoker, $tombstone);
    }

    /**
     * @param array $frame
     *
     * @return string
     */
    private static function getMethodFromTrace($frame)
    {
        if (!is_array($frame)) {
            return null;
        }

        return (isset($frame['class']) ? $frame['class'] . $frame['type'] : '') . $frame['function'];
    }

    /**
     * @param Tombstone $tombstone
     *
     * @return bool
     */
    public function inscriptionEquals(Tombstone $tombstone)
    {
        return $this->tombstone->inscriptionEquals($tombstone);
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
