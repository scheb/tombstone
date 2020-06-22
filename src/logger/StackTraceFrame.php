<?php

declare(strict_types=1);

namespace Scheb\Tombstone;

use Scheb\Tombstone\Tracing\PathNormalizer;

class StackTraceFrame
{
    /**
     * @var string|null
     */
    private $file;

    /**
     * @var int|null
     */
    private $line;

    /**
     * @var string|null
     */
    private $method;

    public function __construct(?string $file, ?int $line, ?string $method)
    {
        $this->file = $file ? PathNormalizer::normalizeDirectorySeparator($file) : null;
        $this->line = $line;
        $this->method = $method;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}
