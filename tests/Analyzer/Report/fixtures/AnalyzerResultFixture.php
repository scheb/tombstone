<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\fixtures;

use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class AnalyzerResultFixture
{
    public static function getAnalyzerResult(): AnalyzerResult
    {
        $rootDir = new RootPath(__DIR__.'/source');
        $functionTombstone = new Tombstone(['2020-01-01', 'globalFunction'], $rootDir->createFilePath('functions.php'), 7, 'globalFunction');
        $globalScope = new Tombstone(['2020-01-01', 'globalScope'], $rootDir->createFilePath('functions.php'), 10, null);
        $class1Tombstone = new Tombstone(['2020-01-01', 'Class1'], $rootDir->createFilePath('Class1.php'), 11, 'Foo\\Class1::staticMethod');
        $class2Tombstone = new Tombstone(['2020-01-01', 'Class2'], $rootDir->createFilePath('Bar/Class2.php'), 11, 'Foo\\Bar\\Class2->publicMethod');
        $class3Tombstone = new Tombstone(['2020-01-01', 'Class3'], $rootDir->createFilePath('Bar/Class3.php'), 11, 'Foo\\Bar\\Class3->someOtherMethod');
        $deletedTombstone = new Tombstone(['2020-01-01', 'Class1'], $rootDir->createFilePath('Class1.php'), 18, 'Foo\\Class1->deletedMethod');

        $vampire1 = new Vampire('2020-02-01', 'invoker1', new StackTrace(), $globalScope, []);
        $globalScope->addVampire($vampire1);
        $vampire2 = new Vampire('2020-02-01', 'invoker2', new StackTrace(), $class2Tombstone, []);
        $vampire3 = new Vampire('2020-02-01', 'invoker3', new StackTrace(), $class2Tombstone, []);
        $class2Tombstone->addVampire($vampire2);
        $class2Tombstone->addVampire($vampire3);
        $vampire4 = new Vampire('2020-02-01', 'invoker4', new StackTrace(), $deletedTombstone, []);

        $deadList = [$functionTombstone, $class1Tombstone, $class3Tombstone];
        $undeadList = [$globalScope, $class2Tombstone];
        $deletedList = [$vampire4];

        return new AnalyzerResult($deadList, $undeadList, $deletedList);
    }
}
