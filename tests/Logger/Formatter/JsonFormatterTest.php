<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Formatter;

use Scheb\Tombstone\Logger\Formatter\JsonFormatter;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

class JsonFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString(): void
    {
        $vampire = Fixture::getVampire('label');
        $formatter = new JsonFormatter();
        $returnValue = $formatter->format($vampire);
        $expectedLog = '{"arguments":["label"],"file":"file","line":123,"method":"method","stackTrace":[{"file":"file1.php","line":11,"method":"ClassName->method"}],"metadata":{"metaField":"metaValue"},"invocationDate":"2015-01-01","invoker":"invoker"}';
        $this->assertEquals($expectedLog.PHP_EOL, $returnValue);
    }
}
