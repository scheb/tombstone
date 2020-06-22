<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Formatter\LineFormatter;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    public function __destruct()
    {
        try {
            $this->flush();
        } catch (\Exception $e) {
            // do nothing
        }
    }

    public function flush(): void
    {
    }

    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): \Scheb\Tombstone\Formatter\FormatterInterface
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    protected function getDefaultFormatter(): \Scheb\Tombstone\Formatter\FormatterInterface
    {
        return new LineFormatter();
    }
}
