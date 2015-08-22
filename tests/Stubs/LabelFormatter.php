<?php
namespace Scheb\Tombstone\Tests\Stubs;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Vampire;

class LabelFormatter implements FormatterInterface
{
    public function format(Vampire $vampire)
    {
        return $vampire->getLabel();
    }
}
