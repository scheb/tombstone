<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Stubs;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Vampire;

class LabelFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return implode(',', $vampire->getArguments());
    }
}
