<?php
namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Cli\Application;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateFactory;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Tracing\PathNormalizer;

class FileRenderer implements ReportGeneratorInterface
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var string
     */
    private $sourceDir;

    /**
     * @var \Text_Template
     */
    private $fileTemplate;

    /**
     * @var \Text_Template
     */
    private $tombstoneTemplate;

    /**
     * @param string $reportDir
     * @param string $sourceDir
     */
    public function __construct($reportDir, $sourceDir)
    {
        $this->reportDir = $reportDir;
        $this->sourceDir = $sourceDir;
        $this->fileTemplate = TemplateFactory::getTemplate('file.html');
        $this->tombstoneTemplate = TemplateFactory::getTemplate('file_tombstone.html');
    }

    /**
     * @param AnalyzerResult $result
     */
    public function generate(AnalyzerResult $result)
    {
        foreach ($result->getPerFile() as $file => $fileResult) {
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $this->renderFile($fileResult);
            }
        }
    }

    /**
     * @param AnalyzerFileResult $fileResult
     */
    private function renderFile(AnalyzerFileResult $fileResult) {
        $tombstonesList = $this->renderTombstonesList($fileResult);
        $sourceCode = $this->formatSourceCode($fileResult);
        $relativeFilePath = PathNormalizer::makeRelativeTo($fileResult->getFile(), $this->sourceDir);
        $this->fileTemplate->setVar(array(
            'path_to_root' => './' . str_repeat('../', substr_count($relativeFilePath, '/')),
            'full_path' => $fileResult->getFile(),
            'breadcrumb' => $this->renderBreadcrumb($relativeFilePath),
            'tombstones_list' => $tombstonesList,
            'source_code' => $sourceCode,
            'date' => date('r'),
            'version' => Application::VERSION,
        ));

        $reportFile = $this->reportDir . DIRECTORY_SEPARATOR . $relativeFilePath . '.html';
        $reportDir = dirname($reportFile);
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
        $this->fileTemplate->renderTo($reportFile);
    }

    /**
     * @param AnalyzerFileResult $fileResult
     *
     * @return string
     */
    private function renderTombstonesList(AnalyzerFileResult $fileResult)
    {
        $tombstoneList = array();

        /** @var Tombstone[] $renderTombstones */
        $renderTombstones = array_merge($fileResult->getDead(), $fileResult->getUndead());
        foreach ($renderTombstones as $tombstone) {
            if (!isset($tombstoneList[$tombstone->getLine()])) {
                $tombstoneList[$tombstone->getLine()] = '';
            }
            $tombstoneList[$tombstone->getLine()] .= $this->renderTombstoneItem(
                $tombstone,
                $tombstone->hasVampires() ? 'danger' : 'success'
            );
        }
        ksort($tombstoneList);

        return implode($tombstoneList);
    }

    /**
     * @param Tombstone $tombstone
     * @param string $class
     *
     * @return string
     */
    private function renderTombstoneItem(Tombstone $tombstone, $class)
    {
        $this->tombstoneTemplate->setVar(array(
            'date' => $tombstone->getTombstoneDate(),
            'author' => $tombstone->getAuthor(),
            'line' => $tombstone->getLine(),
            'level' => $class,
        ));

        return $this->tombstoneTemplate->render();
    }

    /**
     * @param AnalyzerFileResult $fileResult
     *
     * @return string
     */
    private function formatSourceCode(AnalyzerFileResult $fileResult)
    {
        $deadLines = array();
        $undeadLines = array();
        foreach ($fileResult->getDead() as $tombstone) {
            $deadLines[] = $tombstone->getLine();
        }
        foreach ($fileResult->getUndead() as $tombstone) {
            $undeadLines[] = $tombstone->getLine();
        }

        $formattedCode = '';
        $i = 0;
        $code = PhpFileFormatter::loadFile($fileResult->getFile());
        $lineTemplate = '<tr class="%s"><td class="number"><div align="right"><a name="%d"></a><a href="#%d">%d</a></div></td><td class="codeLine">%s</td></tr>';
        foreach ($code as $codeLine) {
            ++$i;

            $class = 'default';
            if (in_array($i, $undeadLines)) {
                $class = 'danger icon-vampire';
            } else if (in_array($i, $deadLines)) {
                $class = 'success icon-cross';
            }

            $formattedCode .= sprintf($lineTemplate, $class, $i, $i, $i, $codeLine);
        }

        return $formattedCode;
    }

    /**
     * @param string $relativeFilePath
     *
     * @return string
     */
    private function renderBreadcrumb($relativeFilePath)
    {
        $parts = explode('/', $relativeFilePath);
        $numParts = count($parts);
        $breadcrumbString = '<li class="active">' . $parts[$numParts - 1] . '</li> ';

        for ($i = 0; $i < $numParts - 1; $i++) {
            array_pop($parts);
            $label = $parts[count($parts) - 1];
            $link = implode('/', $parts) . '/index.html';
            $breadcrumbString .= sprintf('<li><a href="%s">%s</a></li> ', $link, $label);
        }

        return '<li><a href="./' . str_repeat('../', $numParts - 1) . 'index.html">' . $this->sourceDir . '</a></li> ' . $breadcrumbString;
    }
}
