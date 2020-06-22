<?php

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Vampire;

interface HandlerInterface
{
    /**
     * Log a vampire.
     *
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire);

    /**
     * Flush everything.
     */
    public function flush(): void;

    /**
     * Sets the formatter.
     *
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * Gets the formatter.
     *
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface;
}
