<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

class StackTraceFrame
{
    /**
     * @var string
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

    public function __construct(string $file, int $line, ?string $method)
    {
        $this->file = $file;
        $this->line = $line;
        $this->method = $method;
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
}
