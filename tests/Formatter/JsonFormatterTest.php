<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Formatter;

use Scheb\Tombstone\Formatter\JsonFormatter;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

class JsonFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function format_vampireGiven_returnFormattedString(): void
    {
        $vampire = VampireFixture::getVampire('label');
        $formatter = new JsonFormatter();
        $returnValue = $formatter->format($vampire);
        $expectedLog = '{"arguments":["label"],"file":"file","line":123,"method":"method","stackTrace":[{"file":"\/path\/to\/file1.php","line":11,"method":"ClassName->method"}],"metadata":{"metaField":"metaValue"},"invocationDate":"2015-01-01","invoker":"invoker"}';
        $this->assertEquals($expectedLog.PHP_EOL, $returnValue);
    }
}
