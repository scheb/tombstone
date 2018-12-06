<?php

namespace Scheb\Tombstone\Tests\Formatter;

use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Logging\AnalyzerLogFormatTest;

class AnalyzerLogFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString()
    {
        $vampire = VampireFixture::getVampire();
        $formatter = new AnalyzerLogFormatter();
        $returnValue = $formatter->format($vampire);
        $this->assertEquals(AnalyzerLogFormatTest::getLog()."\n", $returnValue);
    }
}
