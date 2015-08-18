<?php

if (!function_exists('tombstone')) {

    /**
     * @param string $date Any date format strtotime() understands
     * @param string $author Your name
     * @param string|null $label An optional label for the tombstone
     */
    function tombstone($date, $author, $label = null) {
        try {
            $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
            \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $label, $trace);
        } catch (\Exception $e) {
        }
    }
}
