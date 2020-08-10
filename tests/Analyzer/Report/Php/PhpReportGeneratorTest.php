<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\Php;

use Scheb\Tombstone\Analyzer\Report\Php\PhpReportGenerator;
use Scheb\Tombstone\Tests\Analyzer\Report\fixtures\AnalyzerResultFixture;
use Scheb\Tombstone\Tests\TestCase;

class PhpReportGeneratorTest extends TestCase
{
    private const EXPORT_FILE = __DIR__.'/export.php';

    protected function setUp(): void
    {
        @unlink(self::EXPORT_FILE);
    }

    protected function tearDown(): void
    {
        @unlink(self::EXPORT_FILE);
    }

    /**
     * @test
     */
    public function generate_resultGiven_exportedDataEqualsOriginalResult(): void
    {
        $result = AnalyzerResultFixture::getAnalyzerResult();

        $generator = new PhpReportGenerator(self::EXPORT_FILE);
        $generator->generate($result);

        $this->assertFileExists(self::EXPORT_FILE);
        $unserializedResult = require self::EXPORT_FILE;

        $this->assertNotSame($result, $unserializedResult);
        $this->assertEquals($result, $unserializedResult);
    }
}
