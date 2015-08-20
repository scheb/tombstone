<?php
namespace Scheb\Tombstone\Tests\Formatter;

use Scheb\Tombstone\Formatter\LogFormatter;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Logging\AnalyzerLogFormatTest;

class LogFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString()
    {
        $vampire = VampireFixture::getVampire();
        $formatter = new LogFormatter();
        $returnValue = $formatter->format($vampire);
        $this->assertEquals(AnalyzerLogFormatTest::getLog() . "\n", $returnValue);
    }
}
