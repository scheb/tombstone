<?php

namespace Scheb\Tombstone\Tracing;

class TraceProvider
{
    public static function getTraceHere(int $skipFrames = 0): array
    {
        ++$skipFrames; // Skip this call
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        return array_splice($trace, $skipFrames, 3);
    }
}
