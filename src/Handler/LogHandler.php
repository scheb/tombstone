<?php
namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Formatter\LogFormatter;

class LogHandler extends StreamHandler
{
    /**
     * @return FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new LogFormatter();
    }
}
