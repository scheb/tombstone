<?php
namespace Scheb\Tombstone\Tracing;

interface TraceProviderInterface {

    /**
     * Return a PHP stack trace
     *
     * @return array
     */
    public function getTrace();
}
