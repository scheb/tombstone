<?php

namespace Scheb\Tombstone\Test\Formatter;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Formatter\LineFormatter;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;

class LineFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString()
    {
        $vampire = VampireFixture::getVampire();
        $formatter = new LineFormatter();
        $returnValue = $formatter->format($vampire);
        $expectedLog = '2015-01-01 - Vampire detected: tombstone("2014-01-01", "author", "label"), in file file:line, in function method, invoked by invoker';
        $this->assertEquals($expectedLog."\n", $returnValue);
    }
}
