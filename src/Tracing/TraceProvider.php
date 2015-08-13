<?php
namespace Scheb\Tombstone\Tracing;

class TraceProvider {

    /**
     * @param integer $skipFrames
     *
     * @return array
     */
    public static function getTrace($skipFrames) {
        ++$skipFrames; // Skip this call
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $skipFrames + 2);
        return array_splice($trace, $skipFrames);
    }
}
