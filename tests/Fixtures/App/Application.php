<?php

namespace Scheb\Tombstone\Analyzer\Test\Fixtures\App;

class Application
{
    public function run()
    {
        deadCodeFunction();
        (new SampleClass())->deadCodeMethod();
    }
}
