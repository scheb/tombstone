<?php

namespace Scheb\Tombstone;

class Vampire
{
    /**
     * @var string
     */
    private $invocationDate;

    /**
     * @var string|null
     */
    private $invoker;

    /**
     * @var Tombstone
     */
    private $tombstone;

    /**
     * @param string      $invocationDate
     * @param string|null $invoker
     * @param Tombstone   $tombstone
     */
    public function __construct(string $invocationDate, ?string $invoker, Tombstone $tombstone)
    {
        $this->invocationDate = $invocationDate;
        $this->invoker = $invoker;
        $this->tombstone = $tombstone;
    }

    public static function createFromCall(string $date, ?string $author, ?string $label, array $trace): Vampire
    {
        // This is the call to the tombstone
        $tombstoneCall = $trace[0];
        $file = $tombstoneCall['file'];
        $line = $tombstoneCall['line'];

        // This is the method with the tombstone contained
        $method = null;
        if (isset($trace[1]) && is_array($trace[1])) {
            $method = self::getMethodFromTrace($trace[1]);
        }

        // This is the method that called the method with the tombstone
        $invoker = null;
        if (isset($trace[2]) && is_array($trace[2]))  {
            $invoker = self::getMethodFromTrace($trace[2]);
        }

        $tombstone = new Tombstone($date, $author, $label, $file, $line, $method);

        return new self(date('c'), $invoker, $tombstone);
    }

    private static function getMethodFromTrace(array $frame): string
    {
        return (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
    }

    public function inscriptionEquals(Tombstone $tombstone): bool
    {
        return $this->tombstone->inscriptionEquals($tombstone);
    }

    public function getInvocationDate(): string
    {
        return $this->invocationDate;
    }

    public function getInvoker(): ?string
    {
        return $this->invoker;
    }

    public function getTombstone(): Tombstone
    {
        return $this->tombstone;
    }

    public function setTombstone(Tombstone $tombstone): void
    {
        $this->tombstone = $tombstone;
    }

    public function getTombstoneDate(): ?string
    {
        return $this->tombstone->getTombstoneDate();
    }

    public function getAuthor(): ?string
    {
        return $this->tombstone->getAuthor();
    }

    public function getLabel(): ?string
    {
        return $this->tombstone->getLabel();
    }

    public function getFile(): string
    {
        return $this->tombstone->getFile();
    }

    public function getLine(): int
    {
        return $this->tombstone->getLine();
    }

    public function getMethod(): ?string
    {
        return $this->tombstone->getMethod();
    }
}
