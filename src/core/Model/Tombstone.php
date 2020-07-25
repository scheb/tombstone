<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

class Tombstone
{
    /**
     * @var array
     * @psalm-type list<string|null>
     */
    private $arguments;

    /**
     * @var string|null
     */
    private $tombstoneDate;

    /**
     * @var FilePathInterface
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var Vampire[]
     */
    private $vampires = [];

    public function __construct(array $arguments, FilePathInterface $file, int $line, ?string $method)
    {
        $this->arguments = $arguments;
        $this->tombstoneDate = $this->findDate($arguments);
        $this->file = $file;
        $this->line = $line;
        $this->method = $method;
    }

    public function __toString(): string
    {
        $argumentsList = '';
        if (\count($this->arguments)) {
            $argumentsList = '"'.implode('", "', $this->arguments).'"';
        }

        return 'tombstone('.$argumentsList.')';
    }

    public function getHash(): string
    {
        return md5($this->file->getReferencePath()."\n".$this->line."\n".implode(',', $this->arguments));
    }

    public function inscriptionEquals(Tombstone $tombstone): bool
    {
        return $tombstone->getArguments() === $this->arguments;
    }

    private function findDate(array $arguments): ?string
    {
        foreach ($arguments as $argument) {
            if (is_scalar($argument) && false !== strtotime((string) $argument)) {
                return (string) $argument;
            }
        }

        return null;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getTombstoneDate(): ?string
    {
        return $this->tombstoneDate;
    }

    public function getFile(): FilePathInterface
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function addVampire(Vampire $vampire): void
    {
        $this->vampires[] = $vampire;
    }

    /**
     * @return Vampire[]
     */
    public function getVampires(): array
    {
        return $this->vampires;
    }

    public function hasVampires(): bool
    {
        return (bool) $this->vampires;
    }
}
