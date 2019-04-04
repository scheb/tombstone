<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tracing;

class TraceProvider
{
    public static function getTraceHere(int $skipFrames = 0): array
    {
        ++$skipFrames; // Skip this call
        $trace = (new \Exception())->getTrace();

        return array_splice($trace, $skipFrames, 3);
    }
}
