<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\TestApplication\App;

class Application
{
    public function run(): void
    {
        deadCodeFunction();
        (new SampleClass())->deadCodeMethod();
        (new DeletedTombstoneClass())->invokeDeletedTombstone();
    }
}
