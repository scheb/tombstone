<?php
namespace Scheb\Tombstone\Tracing;

class TraceProvider
{

    /**
     * @param integer $skipFrames
     *
     * @return array
     */
    public static function getTraceHere($skipFrames = 0)
    {
        ++$skipFrames; // Skip this call
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        return array_splice($trace, $skipFrames, 3);
    }
}
