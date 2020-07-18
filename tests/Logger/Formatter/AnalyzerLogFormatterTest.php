<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Formatter;

use Scheb\Tombstone\Logger\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Tests\Core\Format\AnalyzerLogFormatTest;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

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
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD.PHP_EOL, $returnValue);
    }
}
