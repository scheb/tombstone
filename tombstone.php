<?php

if (!function_exists('tombstone')) {
    function tombstone($date, $author) {
        $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTrace(1);
        \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $trace);
    }
}
