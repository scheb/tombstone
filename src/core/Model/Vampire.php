<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

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
     * @var StackTrace
     */
    private $stackTrace;

    /**
     * @var Tombstone
     */
    private $tombstone;

    /**
     * @var array
     */
    private $metadata;

    public function __construct(string $invocationDate, ?string $invoker, StackTrace $stackTrace, Tombstone $tombstone, array $metadata = [])
    {
        $this->invocationDate = $invocationDate;
        $this->invoker = $invoker;
        $this->stackTrace = $stackTrace;
        $this->tombstone = $tombstone;
        $this->metadata = $metadata;
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

    public function getArguments(): array
    {
        return $this->tombstone->getArguments();
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getTombstoneDate(): ?string
    {
        return $this->tombstone->getTombstoneDate();
    }

    public function getFile(): FilePathInterface
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

    public function getStackTrace(): StackTrace
    {
        return $this->stackTrace;
    }

    public function getHash(): int
    {
        return crc32($this->tombstone->getHash()."\n".$this->invoker);
    }

    public function withTombstone(Tombstone $tombstone): Vampire
    {
        return new Vampire(
            $this->invocationDate,
            $this->invoker,
            $this->stackTrace,
            $tombstone,
            $this->metadata
        );
    }
}
