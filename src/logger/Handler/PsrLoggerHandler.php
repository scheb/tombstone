<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Vampire;

class PsrLoggerHandler extends AbstractHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var mixed
     */
    private $level;

    /**
     * @param mixed           $level
     */
    public function __construct(LoggerInterface $logger, $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    public function log(Vampire $vampire): void
    {
        $this->logger->log($this->level, $this->getFormatter()->format($vampire));
    }
}
