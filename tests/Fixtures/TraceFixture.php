<?php
namespace Scheb\Tombstone\Tests\Fixtures;

class TraceFixture
{
    public static function getTraceFixture()
    {
        return array(
            array(
                'file' => 'file1.php',
                'line' => 11,
                'function' => 'tombstone',
            ),
            array(
                'file' => 'file2.php',
                'line' => 22,
                'function' => 'containingMethodName',
            ),
            array(
                'file' => 'file3.php',
                'line' => 33,
                'function' => 'invokerMethodName',
            )
        );
    }
}
