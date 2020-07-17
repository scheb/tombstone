<?php

declare(strict_types=1);

if (!function_exists('tombstone')) {
    /**
     * @param string ...$arguments
     */
    function tombstone(string ...$arguments): void
    {
        $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
        \Scheb\Tombstone\GraveyardRegistry::getGraveyard()->tombstone($arguments, $trace, []);
    }
}
