<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

class StackTraceFrame
{
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

    public function __construct(FilePathInterface $file, int $line, ?string $method)
    {
        $this->file = $file;
        $this->line = $line;
        $this->method = $method;
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

    public function getHash(): int
    {
        return crc32($this->file->getReferencePath()."\n".$this->line."\n".$this->method);
    }
}
