<?php

if (!function_exists('tombstone')) {
    function tombstone($date, $author, $label = null) {
        try {
            $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
            \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $label, $trace);
        } catch (\Exception $e) {
        }
    }
}
