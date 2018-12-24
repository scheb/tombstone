<?php

namespace Scheb\Tombstone;

class Tombstone
{
    /**
     * @var string|null
     */
    private $tombstoneDate;

    /**
     * @var string|null
     */
    private $author;

    /**
     * @var string|null
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
    private $vampires = [];

    public function __construct(?string $tombstoneDate, ?string $author, ?string $label, string $file, int $line, ?string $method)
    {
        $this->tombstoneDate = $tombstoneDate;
        $this->author = $author;
        $this->file = $file;
        $this->line = $line;
        $this->method = $method;
        $this->label = $label;
    }

    public function __toString(): string
    {
        $label = $this->label ? ', "'.$this->label.'"' : '';

        return sprintf('tombstone("%s", "%s"%s)', $this->tombstoneDate, $this->author, $label);
    }

    public function getHash(): string
    {
        return md5($this->tombstoneDate."\n".$this->author."\n".$this->label."\n".$this->file."\n".$this->line);
    }

    public function inscriptionEquals(Tombstone $tombstone): bool
    {
        return $tombstone->getAuthor() === $this->author && $tombstone->getTombstoneDate() === $this->tombstoneDate && $tombstone->getLabel() === $this->label;
    }

    public function getTombstoneDate(): ?string
    {
        return $this->tombstoneDate;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getFile(): string
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
