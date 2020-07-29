<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Format;

use Scheb\Tombstone\Core\Format\AnalyzerLogFormat;
use Scheb\Tombstone\Core\Format\AnalyzerLogFormatException;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

class AnalyzerLogFormatTest extends TestCase
{
    public const TOMBSTONE_ARGUMENTS = ['2014-01-01', 'label'];
    public const LOG_RECORD = '{"v":10000,"a":["2014-01-01","label"],"f":"file","l":123,"m":"method","d":{"metaField":"metaValue"},"s":[{"f":"\/path\/to\/file1.php","l":11,"m":"ClassName->method"}],"id":"2015-01-01","im":"invoker"}';

    /**
     * @test
     */
    public function vampireToLog_formatVampire_returnLogFormat(): void
    {
        $vampire = VampireFixture::getVampire(...self::TOMBSTONE_ARGUMENTS);
        $returnValue = AnalyzerLogFormat::vampireToLog($vampire);
        $this->assertEquals(self::LOG_RECORD, $returnValue);
    }

    /**
     * @test
     */
    public function logToVampire_invalidVersion_throwException(): void
    {
        $this->expectException(AnalyzerLogFormatException::class);
        $this->expectExceptionCode(AnalyzerLogFormatException::INCOMPATIBLE_VERSION);

        AnalyzerLogFormat::logToVampire('{"v":1}', new RootPath(__DIR__));
    }

    /**
     * @test
     */
    public function logToVampire_malformedData_throwException(): void
    {
        $this->expectException(AnalyzerLogFormatException::class);
        $this->expectExceptionCode(AnalyzerLogFormatException::MISSING_DATA);

        AnalyzerLogFormat::logToVampire('{', new RootPath(__DIR__));
    }

    /**
     * @test
     */
    public function logToVampire_missingData_throwException(): void
    {
        $this->expectException(AnalyzerLogFormatException::class);
        $this->expectExceptionCode(AnalyzerLogFormatException::MISSING_DATA);

        AnalyzerLogFormat::logToVampire('{"v":10000}', new RootPath(__DIR__));
    }

    /**
     * @test
     */
    public function logToVampire_missingDataInStackTrace_truncateStackTrace(): void
    {
        $returnValue = AnalyzerLogFormat::logToVampire('{"v":10000,"a":[],"f":"file","l":123,"s":[{"f":"file1","l":1},{"f":"file2"},{"f":"file3","l":3}],"id":"2015-01-01"}', new RootPath(__DIR__));

        $this->assertCount(1, $returnValue->getStackTrace());
    }

    /**
     * @test
     */
    public function logToVampire_validLog_returnVampire(): void
    {
        $returnValue = AnalyzerLogFormat::logToVampire(self::LOG_RECORD, new RootPath(VampireFixture::ROOT_DIR));
        $expectedVampire = VampireFixture::getVampire(...self::TOMBSTONE_ARGUMENTS);
        $this->assertEquals($expectedVampire, $returnValue);
    }
}
