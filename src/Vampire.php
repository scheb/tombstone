<?php
namespace Scheb\Tombstone;

class Vampire
{

    /**
     * @var string
     */
    private $awakeningDate;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $tombstoneDate;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string|null
     */
    private $invoker;

    /**
     * @param string $tombstoneDate
     * @param string $author
     * @param string $fileName
     * @param int $line
     * @param string $method
     * @param string|null $invoker
     */
    public function __construct($tombstoneDate, $author, $fileName, $line, $method, $invoker)
    {
        $this->awakeningDate = date('c');
        $this->author = $author;
        $this->tombstoneDate = $tombstoneDate;
        $this->fileName = $fileName;
        $this->line = $line;
        $this->method = $method;
        $this->invoker = $invoker;
    }

    /**
     * @param string $date
     * @param string $author
     * @param array $trace
     * @return Vampire
     */
    public static function create($date, $author, $trace)
    {
        $firstFrame = $trace[0];
        $secondFrame = isset($trace[1]) ? $trace[1] : null;
        $file = $firstFrame['file'];
        $line = $firstFrame['line'];
        $method = self::getMethodFromTrace($firstFrame);
        $invoker = $secondFrame ? self::getMethodFromTrace($secondFrame) : null;

        return new self($date, $author, $file, $line, $method, $invoker);
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
    public function getFileName()
    {
        return $this->fileName;
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
     * @return null|string
     */
    public function getInvoker()
    {
        return $this->invoker;
    }
}
