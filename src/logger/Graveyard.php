<?php

declare(strict_types=1);

namespace Scheb\Tombstone;

use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Tracing\PathNormalizer;
use Scheb\Tombstone\Tracing\TraceProvider;

class Graveyard implements GraveyardInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var TraceProvider
     */
    private $traceProvider;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var int|null
     */
    private $stackTraceDepth;

    public function __construct(array $handlers = [], ?string $rootDir = null, ?int $stackTraceDepth = null)
    {
        $this->handlers = $handlers;
        $this->traceProvider = new TraceProvider();
        $this->stackTraceDepth = $stackTraceDepth;
        $this->setRootDir($rootDir);
    }

    public function setRootDir($rootDir): void
    {
        $this->rootDir = $rootDir;
    }

    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function tombstone(array $arguments, array $trace, array $metadata): void
    {
        $trace = $this->sliceTrace($trace);
        $trace = $this->traceRelativePath($trace);
        $vampire = Vampire::createFromCall($arguments, $trace, $metadata);
        foreach ($this->handlers as $handler) {
            $handler->log($vampire);
        }
    }

    private function traceRelativePath(array $trace): array
    {
        if (!$this->rootDir) {
            return $trace;
        }

        foreach ($trace as $key => &$frame) {
            if (isset($frame['file'])) {
                $frame['file'] = PathNormalizer::makeRelativeTo($frame['file'], $this->rootDir);
            }
        }

        return $trace;
    }

    private function sliceTrace(array $trace): array
    {
        if ($this->stackTraceDepth > 0) {
            return \array_slice($trace, 0, $this->stackTraceDepth);
        }

        return $trace;
    }

    public function flush(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->flush();
        }
    }
}
