<?php
namespace Scheb\Tombstone\Tracing;

class TraceProvider implements TraceProviderInterface {

    /**
     * @var int
     */
    private $skipFrames;

    /**
     * @param int $skipFrames
     */
    public function __construct($skipFrames = 0){
        $this->skipFrames = $skipFrames + 1; // Skip one by default to remove trace of the class itself
    }

    /**
     * @return array
     */
    public function getTrace() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $this->skipFrames + 2);
        return array_splice($trace, $this->skipFrames);
    }
}
