<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class VampireFactory
{
    /**
     * @var RootPath
     */
    private $rootPath;

    /**
     * @var int
     */
    private $stackTraceDepth;

    public function __construct(RootPath $rootPath, int $stackTraceDepth)
    {
        $this->rootPath = $rootPath;
        $this->stackTraceDepth = $stackTraceDepth;
    }

    public function createFromCall(array $arguments, array $trace, array $metadata): Vampire
    {
        // This is the call to the tombstone
        $tombstoneCall = $trace[0];
        $file = $this->rootPath->createFilePath($tombstoneCall['file']);
        $line = $tombstoneCall['line'];
        $functionName = $this->getTombstoneFunctionName($tombstoneCall);

        // This is the method containing the tombstone
        $method = null;
        if (isset($trace[1]) && \is_array($trace[1])) {
            $method = $this->getMethodFromFrame($trace[1]);
        }

        // This is the method that called the method with the tombstone
        $invoker = null;
        if (isset($trace[2]) && \is_array($trace[2])) {
            $invoker = $this->getMethodFromFrame($trace[2]);
        }

        $tombstone = new Tombstone($functionName, $arguments, $file, $line, $method);

        $stackTrace = null;
        if ($this->stackTraceDepth > 0) {
            $trace = \array_slice($trace, 0, $this->stackTraceDepth);
            $stackTrace = $this->createStackTrace($trace);
        }

        return new Vampire(date('c'), $invoker, $stackTrace ?? new StackTrace(), $tombstone, $metadata);
    }

    private function getTombstoneFunctionName(array $frame): string
    {
        // Always using :: here because we want the fully qualified name of the tombstone function and not how it was
        // called (static vs. non-static).
        return (isset($frame['class']) ? $frame['class'].'::' : '').$frame['function'];
    }

    private function getMethodFromFrame(array $frame): string
    {
        return (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
    }

    private function createStackTrace(array $trace): StackTrace
    {
        $frames = [];
        foreach ($trace as $frame) {
            $frames[] = new StackTraceFrame(
                $this->rootPath->createFilePath($frame['file'] ?? '/'),
                $frame['line'] ?? 0,
                self::getMethodFromFrame($frame)
            );
        }

        return new StackTrace(...$frames);
    }
}
