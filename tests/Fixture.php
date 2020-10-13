<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class Fixture
{
    public const ROOT_DIR = '/path/to';
    public const NUMBER_OF_FRAMES = 5;

    public static function getVampire(string ...$arguments): Vampire
    {
        $rootPath = new RootPath(self::ROOT_DIR);
        $tombstone = new Tombstone('tombstone', $arguments, $rootPath->createFilePath('file'), 123, 'method');
        $stackTrace = new StackTrace(new StackTraceFrame($rootPath->createFilePath('/path/to/file1.php'), 11, 'ClassName->method'));

        return new Vampire('2015-01-01', 'invoker', $stackTrace, $tombstone, ['metaField' => 'metaValue']);
    }

    public static function getTombstone(string ...$arguments): Tombstone
    {
        $rootPath = new RootPath(self::ROOT_DIR);

        return new Tombstone('tombstone', $arguments, $rootPath->createFilePath('file'), 123, 'method');
    }

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
            [
                'function' => '__destruct',
                'class' => 'ClassName',
                'type' => '->',
            ],
        ];
    }
}
