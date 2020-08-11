<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Scheb\Tombstone\Logger\Handler\HandlerInterface;

class Graveyard implements GraveyardInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var VampireFactory
     */
    private $vampireFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(VampireFactory $vampireFactory, ?LoggerInterface $logger, array $handlers = [])
    {
        $this->vampireFactory = $vampireFactory;
        $this->logger = $logger ?? new NullLogger();
        $this->handlers = $handlers;
    }

    public function logTombstoneCall(string $functionName, array $arguments, array $trace, array $metadata): void
    {
        try {
            $vampire = $this->vampireFactory->createFromCall($functionName, $arguments, $trace, $metadata);
            foreach ($this->handlers as $handler) {
                $handler->log($vampire);
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Exception while tracking a tombstone call: %s %s (%s)', \get_class($e), $e->getMessage(), $e->getCode()));
        }
    }

    public function flush(): void
    {
        try {
            foreach ($this->handlers as $handler) {
                $handler->flush();
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Exception while flushing tombstones: %s %s (%s)', \get_class($e), $e->getMessage(), $e->getCode()));
        }
    }
}
