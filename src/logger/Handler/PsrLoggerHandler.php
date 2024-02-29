<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Handler;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Core\Model\Vampire;

class PsrLoggerHandler extends AbstractHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string|int|mixed
     */
    private $level;

    /**
     * @param string|int|mixed $level
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
