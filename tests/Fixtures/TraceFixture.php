<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Fixtures;

class TraceFixture
{
    public const NUMBER_OF_FRAMES = 4;

    public static function getTraceFixture(): array
    {
        return [
            [
                'file' => '/path/to/file1.php',
                'line' => 11,
                'function' => 'tombstone',
            ],
            [
                'file' => '/path/to/file2.php',
                'line' => 22,
                'function' => 'containingMethodName',
            ],
            [
                'file' => '/path/to/file3.php',
                'line' => 33,
                'function' => 'invokerMethodName',
            ],
            [
                'file' => 'C:\\path\\to\\file4.php',
                'line' => 44,
                'class' => 'ClassName',
                'type' => '->',
                'function' => 'invokerInvokerMethodName',
            ],
        ];
    }
}
