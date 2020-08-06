<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use SebastianBergmann\Template\Template;

class FileSourceCodeRenderer
{
    /**
     * @var PhpFileFormatter
     */
    private $fileFormatter;

    /**
     * @var Template|\Text_Template
     */
    private $sourceCodeTemplate;

    public function __construct(PhpFileFormatter $fileFormatter)
    {
        $this->fileFormatter = $fileFormatter;
        $this->sourceCodeTemplate = TemplateProvider::getTemplate('file_source_code.html');
    }

    public function renderSourceCode(AnalyzerFileResult $fileResult): string
    {
        $deadLines = [];
        $undeadLines = [];
        foreach ($fileResult->getDead() as $tombstone) {
            $deadLines[$tombstone->getLine()] = true;
        }
        foreach ($fileResult->getUndead() as $tombstone) {
            $undeadLines[$tombstone->getLine()] = true;
        }

        $formattedCode = '';
        $lineNumber = 0;
        $code = $this->fileFormatter->formatFile($fileResult->getFile()->getAbsolutePath());
        foreach ($code as $codeLine) {
            ++$lineNumber;

            $class = 'default';
            if (isset($undeadLines[$lineNumber])) {
                $class = 'danger icon-vampire';
            } elseif (isset($deadLines[$lineNumber])) {
                $class = 'success icon-cross';
            }

            $formattedCode .= $this->renderCodeLine($class, $lineNumber, $codeLine);
        }

        return $formattedCode;
    }

    private function renderCodeLine(string $class, int $lineNumber, string $codeLine): string
    {
        $this->sourceCodeTemplate->setVar([
            'class' => $class,
            'line' => $lineNumber,
            'code' => $codeLine,
        ]);

        return $this->sourceCodeTemplate->render();
    }
}
