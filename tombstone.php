<?php

if (!function_exists('tombstone')) {
    /**
     * @param string      $date   Any date format strtotime() understands
     * @param string|null $author Your name
     * @param string|null $label  An optional label for the tombstone
     */
    function tombstone(string $date, ?string $author = null, ?string $label = null)
    {
        try {
            $trace = \Scheb\Tombstone\Tracing\TraceProvider::getTraceHere();
            \Scheb\Tombstone\GraveyardProvider::getGraveyard()->tombstone($date, $author, $label, $trace);
        } catch (\Exception $e) {
        }
    }
}
