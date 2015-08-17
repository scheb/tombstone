<?php

if (!function_exists('tombstone')) {
    function tombstone($date, $author) {
        try {
            $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
            \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $trace);
        } catch (\Exception $e) {
        }
    }
}
