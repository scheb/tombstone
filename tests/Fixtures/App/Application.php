<?php

namespace Scheb\Tombstone\Analyzer\Test\Fixtures\App;

class Application
{
    public function run(): void
    {
        deadCodeFunction();
        (new SampleClass())->deadCodeMethod();
    }
}
