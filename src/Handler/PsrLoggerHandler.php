<?php

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
     * @param LoggerInterface $logger
     * @param mixed           $level
     */
    public function __construct(LoggerInterface $logger, $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * Log a vampire.
     *
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire)
    {
        $this->logger->log($this->level, $this->getFormatter()->format($vampire));
    }
}
