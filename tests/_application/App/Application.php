<?php

namespace Scheb\Tombstone\Analyzer\TestApplication\App;

class Application
{
    public function run(): void
    {
        deadCodeFunction();
        (new SampleClass())->deadCodeMethod();
    }
}
