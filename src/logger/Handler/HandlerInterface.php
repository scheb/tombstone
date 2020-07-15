<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Vampire;

interface HandlerInterface
{
    /**
     * Log a vampire.
     */
    public function log(Vampire $vampire): void;

    /**
     * Flush everything.
     */
    public function flush(): void;

    /**
     * Sets the formatter.
     */
    public function setFormatter(FormatterInterface $formatter): void;

    /**
     * Gets the formatter.
     */
    public function getFormatter(): FormatterInterface;
}
