<?php

namespace Scheb\Tombstone\Test\Fixtures;

class TraceFixture
{
    public static function getTraceFixture(): array
    {
        return array(
            array(
                'file' => '/path/to/file1.php',
                'line' => 11,
                'function' => 'tombstone',
            ),
            array(
                'file' => '/path/to/file2.php',
                'line' => 22,
                'function' => 'containingMethodName',
            ),
            array(
                'file' => '/path/to/file3.php',
                'line' => 33,
                'function' => 'invokerMethodName',
            ),
        );
    }
}
