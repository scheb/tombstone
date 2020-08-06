<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpFileFormatter;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpSyntaxHighlighter;
use Scheb\Tombstone\Tests\TestCase;

class PhpFileFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function loadFile_phpFileGiven_formattedHtmlReturned()
    {
        $formatter = new PhpFileFormatter(new PhpSyntaxHighlighter());
        $formattedLines = $formatter->formatFile(__DIR__.'/fixtures/FormattingTestClass.php');
        $formattedFile = implode("<br />\n", $formattedLines);
        file_put_contents(__DIR__.'/fixtures/FormattingTestClass.actual.html', $formattedFile);
        $this->assertStringEqualsFile(__DIR__.'/fixtures/FormattingTestClass.html', $formattedFile);
    }
}
