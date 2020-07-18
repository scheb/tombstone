<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Model\Vampire;

class LabelFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return implode(',', $vampire->getArguments());
    }
}
