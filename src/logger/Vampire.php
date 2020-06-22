<?php

declare(strict_types=1);

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
     * @var array
     */
    private $stackTrace;

    /**
     * @var Tombstone
     */
    private $tombstone;

    public function __construct(?string $invocationDate, ?string $invoker, array $stackTrace, Tombstone $tombstone)
    {
        $this->invocationDate = $invocationDate;
        $this->invoker = $invoker;
        $this->stackTrace = $stackTrace;
        $this->tombstone = $tombstone;
    }

    public static function createFromCall(array $arguments, array $trace, array $metadata): Vampire
    {
        // This is the call to the tombstone
        $tombstoneCall = $trace[0];
        $file = $tombstoneCall['file'];
        $line = $tombstoneCall['line'];

        // This is the method with the tombstone contained
        $method = null;
        if (isset($trace[1]) && is_array($trace[1])) {
            $method = self::getMethodFromFrame($trace[1]);
        }

        // This is the method that called the method with the tombstone
        $invoker = null;
        if (isset($trace[2]) && is_array($trace[2])) {
            $invoker = self::getMethodFromFrame($trace[2]);
        }

        $tombstone = new Tombstone($arguments, $file, $line, $method, $metadata);
        $stackTrace = self::createStackTrace($trace);

        return new self(date('c'), $invoker, $stackTrace, $tombstone);
    }

    private static function getMethodFromFrame(array $frame): string
    {
        return (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
    }

    private static function createStackTrace(array $trace): array
    {
        $stackTrace = [];
        foreach ($trace as $traceElement) {
            $stackTrace[] = new StackTraceFrame(
                $traceElement['file'] ?? null,
                $traceElement['line'] ?? null,
                self::getMethodFromFrame($traceElement)
            );
        }

        return $stackTrace;
    }

    public function inscriptionEquals(Tombstone $tombstone): bool
    {
        return $this->tombstone->inscriptionEquals($tombstone);
    }

    public function getInvocationDate(): ?string
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

    public function getArguments(): array
    {
        return $this->tombstone->getArguments();
    }

    public function getMetadata(): array
    {
        return $this->tombstone->getMetadata();
    }

    public function getTombstoneDate(): ?string
    {
        return $this->tombstone->getTombstoneDate();
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

    /**
     * @return StackTraceFrame[]
     */
    public function getStackTrace(): array
    {
        return $this->stackTrace;
    }

    public function getStackTraceHash(): string
    {
        return sha1(serialize($this->stackTrace));
    }
}
