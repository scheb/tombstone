<?php

if (!function_exists('tombstone')) {
    function tombstone($date, $author) {
        $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTrace(0);
        \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $trace);
    }
}
