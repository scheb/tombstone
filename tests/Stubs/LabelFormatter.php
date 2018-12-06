<?php

namespace Scheb\Tombstone\Test\Stubs;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Vampire;

class LabelFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return $vampire->getLabel() ?? '';
    }
}
