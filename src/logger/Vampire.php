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

    public function __construct(string $invocationDate, ?string $invoker, array $stackTrace, Tombstone $tombstone)
    {
        $this->invocationDate = $invocationDate;
        $this->invoker = $invoker;
        $this->stackTrace = $stackTrace;
        $this->tombstone = $tombstone;
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
