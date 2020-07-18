<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Formatter\LineFormatter;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormatterInterface|null
     */
    protected $formatter;

    public function __destruct()
    {
        $this->flush();
    }

    public function flush(): void
    {
    }

    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function getFormatter(): FormatterInterface
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
