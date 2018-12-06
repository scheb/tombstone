<?php

namespace Scheb\Tombstone\Test\Formatter;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Logging\AnalyzerLogFormatTest;

class AnalyzerLogFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString(): void
    {
        $vampire = VampireFixture::getVampire();
        $formatter = new AnalyzerLogFormatter();
        $returnValue = $formatter->format($vampire);
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD."\n", $returnValue);
    }
}
