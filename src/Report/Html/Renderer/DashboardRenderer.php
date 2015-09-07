<?php
namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Cli\Application;
use Scheb\Tombstone\Analyzer\Report\Console\TimePeriodFormatter;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateFactory;
use Scheb\Tombstone\Analyzer\Report\PathTools;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Tracing\PathNormalizer;

class DashboardRenderer implements ReportGeneratorInterface
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
    private $dashboardTemplate;

    /**
     * @var \Text_Template
     */
    private $fileTemplate;

    /**
     * @var \Text_Template
     */
    private $deadTemplate;

    /**
     * @var \Text_Template
     */
    private $undeadTemplate;

    /**
     * @var \Text_Template
     */
    private $deletedTemplate;

    /**
     * @var \Text_Template
     */
    private $invokerTemplate;

    /**
     * @param string $reportDir
     * @param $sourceDir
     */
    public function __construct($reportDir, $sourceDir)
    {
        $this->reportDir = $reportDir;
        $this->sourceDir = $sourceDir;
        $this->dashboardTemplate = TemplateFactory::getTemplate('dashboard.html');
        $this->fileTemplate = TemplateFactory::getTemplate('dashboard_file.html');
        $this->deadTemplate = TemplateFactory::getTemplate('dashboard_dead.html');
        $this->undeadTemplate = TemplateFactory::getTemplate('dashboard_undead.html');
        $this->deletedTemplate = TemplateFactory::getTemplate('dashboard_deleted.html');
        $this->invokerTemplate = TemplateFactory::getTemplate('dashboard_invoker.html.dist');
    }

    /**
     * @param AnalyzerResult $result
     */
    public function generate(AnalyzerResult $result)
    {
        $tombstonesView = $this->renderTombstonesView($result);
        $deletedView = $this->renderDeletedView($result);

        $numUndead = count($result->getUndead());
        $numDead = count($result->getDead());
        $numDeleted = count($result->getDeleted());
        $total = $numDead + $numUndead;

        $deadPercent = $total ? $numDead / $total * 100 : 0;
        $undeadPercent = $total ? $numUndead / $total * 100 : 0;

        $this->dashboardTemplate->setVar(array(
            'path_to_root' => './',
            'tombstones_count' => $total,
            'dead_count' => $numDead,
            'undead_count' => $numUndead,
            'deleted_count' => $numDeleted,
            'dead_percent' => $deadPercent,
            'undead_percent' => $undeadPercent,
            'tombstones_view' => $tombstonesView,
            'deleted_view' => $deletedView,
            'full_path' => $this->sourceDir,
            'version' => Application::VERSION,
            'date' => date('r'),
        ));
        $this->dashboardTemplate->renderTo($this->reportDir . DIRECTORY_SEPARATOR . 'dashboard.html');
    }

    /**
     * @param AnalyzerResult $result
     *
     * @return string
     */
    private function renderTombstonesView(AnalyzerResult $result)
    {
        $tombstonesView = '';
        foreach ($result->getPerFile() as $fileResult) {
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $itemList = $this->renderUndeadTombstones($fileResult);
                $itemList .= $this->renderDeadTombstones($fileResult);

                $tombstonesView .= $this->renderFile($fileResult->getFile(), $itemList);
            }
        }

        return $tombstonesView;
    }

    /**
     * @param AnalyzerFileResult $fileResult
     *
     * @return string
     */
    private function renderDeadTombstones(AnalyzerFileResult $fileResult)
    {
        $itemList = '';
        foreach ($fileResult->getDead() as $tombstone) {
            $date = $tombstone->getTombstoneDate();
            if ($age = TimePeriodFormatter::formatAge($date)) {
                $deadSince = 'for ' . $age;
            } else {
                $deadSince = 'since ' . $date;
            }
            $this->deadTemplate->setVar(array(
                'path_to_root' => './',
                'tombstone' => $this->linkTombstoneSource((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'method' => $tombstone->getMethod(),
                'dead_since' => $deadSince,
            ));
            $itemList .= $this->deadTemplate->render();
        }

        return $itemList;
    }

    /**
     * @param AnalyzerFileResult $fileResult
     *
     * @return string
     */
    private function renderUndeadTombstones(AnalyzerFileResult $fileResult)
    {
        $itemList = '';
        foreach ($fileResult->getUndead() as $tombstone) {
            $invocation = $this->renderInvokers($tombstone);
            $this->undeadTemplate->setVar(array(
                'path_to_root' => './',
                'tombstone' => $this->linkTombstoneSource((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'method' => $tombstone->getMethod(),
                'invocation' => $invocation,
            ));
            $itemList .= $this->undeadTemplate->render();
        }

        return $itemList;
    }

    /**
     * @param AnalyzerResult $result
     *
     * @return string
     */
    private function renderDeletedView(AnalyzerResult $result)
    {
        $deletedView = '';
        foreach ($result->getPerFile() as $fileResult) {
            if ($fileResult->getDeletedCount()) {
                $absoluteFilePath = PathTools::makePathAbsolute($fileResult->getFile(), $this->sourceDir);
                $deletedView .= $this->renderFile($absoluteFilePath, $this->renderDeletedTombstones($fileResult));
            }
        }

        return $deletedView;
    }

    /**
     * @param AnalyzerFileResult $fileResult
     *
     * @return string
     */
    private function renderDeletedTombstones(AnalyzerFileResult $fileResult)
    {
        $itemList = '';
        foreach ($fileResult->getDeleted() as $vampire) {
            $this->deletedTemplate->setVar(array(
                'path_to_root' => './',
                'tombstone' => (string) $vampire->getTombstone(),
                'line' => $vampire->getLine(),
                'method' => $vampire->getMethod(),
                'last_call' => TimePeriodFormatter::formatAge($vampire->getInvocationDate()),
            ));
            $itemList .= $this->deletedTemplate->render();
        }

        return $itemList;
    }

    /**
     * @param string $fileName
     * @param string $itemList
     *
     * @return string
     */
    private function renderFile($fileName, $itemList)
    {
        $this->fileTemplate->setVar(array(
            'file' => $fileName,
            'item_list' => $itemList,
        ));

        return $this->fileTemplate->render();
    }

    /**
     * @param Tombstone $tombstone
     *
     * @return string
     */
    private function renderInvokers(Tombstone $tombstone)
    {
        $invokers = array();
        foreach ($tombstone->getVampires() as $vampire) {
            $invokers[] = $vampire->getInvoker();
        }
        $invokers = array_unique($invokers);
        $invokersString = '';
        foreach ($invokers as $invoker) {
            $this->invokerTemplate->setVar(array(
                'invoker' => $invoker ?: 'global scope',
            ));
            $invokersString .= $this->invokerTemplate->render();
        }

        return $invokersString;
    }

    /**
     * @param string $label
     * @param string $fileName
     * @param int $line
     *
     * @return string
     */
    private function linkTombstoneSource($label, $fileName, $line)
    {
        $relativePath = PathNormalizer::makeRelativeTo($fileName, $this->sourceDir);
        return sprintf('<a href="./%s.html#%s">%s</a>', $relativePath, $line, $label);
    }
}
