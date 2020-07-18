<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Handler;

use Scheb\Tombstone\Logger\Formatter\FormatterInterface;
use Scheb\Tombstone\Logger\Formatter\LineFormatter;

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
