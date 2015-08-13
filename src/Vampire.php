<?php
namespace Scheb\Tombstone;

class Vampire extends Tombstone
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
     * @param string $awakeningDate
     * @param string $tombstoneDate
     * @param string $author
     * @param string $fileName
     * @param int $line
     * @param string $method
     * @param string|null $invoker
     */
    public function __construct($awakeningDate, $tombstoneDate, $author, $fileName, $line, $method, $invoker)
    {
        parent::__construct($tombstoneDate, $author, $fileName, $line, $method);
        $this->awakeningDate = $awakeningDate;
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

        return new self(date('c'), $date, $author, $file, $line, $method, $invoker);
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
}
