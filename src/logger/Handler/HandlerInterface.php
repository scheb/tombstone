<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Handler;

use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Formatter\FormatterInterface;

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
