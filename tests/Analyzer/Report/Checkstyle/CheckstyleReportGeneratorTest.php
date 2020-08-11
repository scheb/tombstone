<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\Checkstyle;

use Scheb\Tombstone\Analyzer\Report\Checkstyle\CheckstyleReportGenerator;
use Scheb\Tombstone\Tests\Analyzer\Report\fixtures\AnalyzerResultFixture;
use Scheb\Tombstone\Tests\TestCase;

class CheckstyleReportGeneratorTest extends TestCase
{
    private const EXPORT_FILE = __DIR__.'/checkstyle.actual.xml';

    /**
     * @test
     */
    public function generate_resultGiven_generateXmlFile(): void
    {
        $result = AnalyzerResultFixture::getAnalyzerResult();

        $generator = new CheckstyleReportGenerator(self::EXPORT_FILE);
        $generator->generate($result);

        $this->assertFileExists(self::EXPORT_FILE);
        $output = file_get_contents(self::EXPORT_FILE);
        $this->assertStringMatchesFormatFile(__DIR__.'/checkstyle.xml', $output);
    }
}
