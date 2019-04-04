<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpFileFormatter;
use Scheb\Tombstone\Analyzer\Test\TestCase;

class PhpFileFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function loadFile_phpFileGiven_formattedHtmlReturned()
    {
        $formattedLines = PhpFileFormatter::loadFile(__DIR__.'/Fixtures/FormattingTestClass.php');
        $formattedFile = implode("<br />\n", $formattedLines);
        $this->assertStringEqualsFile(__DIR__.'/Fixtures/FormattingTestClass.html', $formattedFile);
    }
}
