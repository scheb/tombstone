<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Formatter;

use Scheb\Tombstone\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Logging\AnalyzerLogFormatTest;
use Scheb\Tombstone\Tests\TestCase;

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
