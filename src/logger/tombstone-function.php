<?php

declare(strict_types=1);

if (!function_exists('tombstone')) {
    /**
     * @param string ...$arguments
     */
    function tombstone(string ...$arguments): void
    {
        try {
            $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
            \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($arguments, $trace, []);
        } catch (\Exception $e) {
        }
    }
}
