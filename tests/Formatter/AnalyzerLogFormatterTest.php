<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Formatter;

use Scheb\Tombstone\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Logging\AnalyzerLogFormatTest;
use Scheb\Tombstone\Test\TestCase;

class AnalyzerLogFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString(): void
    {
        $vampire = VampireFixture::getVampire(...AnalyzerLogFormatTest::TOMBSTONE_ARGUMENTS);
        $formatter = new AnalyzerLogFormatter();
        $returnValue = $formatter->format($vampire);
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD."\n", $returnValue);
    }
}
