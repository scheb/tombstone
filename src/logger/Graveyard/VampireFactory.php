<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Tracing\PathNormalizer;

class VampireFactory
{
    /**
     * @var string|null
     */
    private $rootDir;

    /**
     * @var int
     */
    private $stackTraceDepth;

    public function __construct(?string $rootDir, int $stackTraceDepth)
    {
        if (null !== $rootDir) {
            $this->rootDir = $rootDir;

            // Use the real path if possible
            $rootDirRealPath = realpath($this->rootDir);
            if ($rootDirRealPath) {
                $this->rootDir = $rootDirRealPath;
            }
        }

        $this->stackTraceDepth = $stackTraceDepth;
    }

    public function createFromCall(array $arguments, array $trace, array $metadata): Vampire
    {
        // This is the call to the tombstone
        $tombstoneCall = $trace[0];
        $file = $this->normalizeAndRelativePath($tombstoneCall['file']);
        $line = $tombstoneCall['line'];

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

        $tombstone = new Tombstone($arguments, $file, $line, $method, $metadata);

        $stackTrace = [];
        if ($this->stackTraceDepth > 0) {
            $trace = \array_slice($trace, 0, $this->stackTraceDepth);
            $stackTrace = $this->createStackTrace($trace);
        }

        return new Vampire(date('c'), $invoker, $stackTrace, $tombstone);
    }

    private function getMethodFromFrame(array $frame): string
    {
        return (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
    }

    private function createStackTrace(array $trace): array
    {
        $stackTrace = [];
        foreach ($trace as $frame) {
            $stackTrace[] = new StackTraceFrame(
                $this->normalizeAndRelativePath($frame['file']),
                $frame['line'],
                self::getMethodFromFrame($frame)
            );
        }

        return $stackTrace;
    }

    private function normalizeAndRelativePath(string $path): string
    {
        $path = PathNormalizer::normalizeDirectorySeparator($path);

        if (null === $this->rootDir) {
            return $path;
        }

        return PathNormalizer::makeRelativeTo($path, $this->rootDir);
    }
}
