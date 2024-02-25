<?php

declare(strict_types=1);

if (!function_exists('tombstone')) {
    function tombstone(string ...$arguments): void
    {
        $trace = Scheb\Tombstone\Logger\Tracing\TraceProvider::getTraceHere();
        Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry::getGraveyard()->logTombstoneCall($arguments, $trace, []);
    }
}
