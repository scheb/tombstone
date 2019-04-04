<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

interface FormatterInterface
{
    /**
     * Formats a Vampire for the log.
     *
     * @param Vampire $vampire
     *
     * @return string
     */
    public function format(Vampire $vampire): string;
}
