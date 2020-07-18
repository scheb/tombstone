<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Handler;

use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Formatter\FormatterInterface;

class LabelFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return implode(',', $vampire->getArguments());
    }
}
