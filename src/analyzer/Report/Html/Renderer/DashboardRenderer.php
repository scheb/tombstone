<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\Console\TimePeriodFormatter;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateFactory;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Tracing\PathNormalizer;

class DashboardRenderer
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var string
     */
    private $rootDir;

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

    public function __construct(string $reportDir, string $rootDir)
    {
        $this->reportDir = $reportDir;
        $this->rootDir = $rootDir;
        $this->dashboardTemplate = TemplateFactory::getTemplate('dashboard.html');
        $this->fileTemplate = TemplateFactory::getTemplate('dashboard_file.html');
        $this->deadTemplate = TemplateFactory::getTemplate('dashboard_dead.html');
        $this->undeadTemplate = TemplateFactory::getTemplate('dashboard_undead.html');
        $this->deletedTemplate = TemplateFactory::getTemplate('dashboard_deleted.html');
        $this->invokerTemplate = TemplateFactory::getTemplate('dashboard_invoker.html.dist');
    }

    public function generate(AnalyzerResult $result): void
    {
        $tombstonesView = $this->renderTombstonesView($result);
        $deletedView = $this->renderDeletedView($result);

        $numUndead = \count($result->getUndead());
        $numDead = \count($result->getDead());
        $numDeleted = \count($result->getDeleted());
        $total = $numDead + $numUndead;

        $deadPercent = $total ? $numDead / $total * 100 : 0;
        $undeadPercent = $total ? $numUndead / $total * 100 : 0;

        $this->dashboardTemplate->setVar([
            'path_to_root' => './',
            'tombstones_count' => $total,
            'dead_count' => $numDead,
            'undead_count' => $numUndead,
            'deleted_count' => $numDeleted,
            'dead_percent' => $deadPercent,
            'undead_percent' => $undeadPercent,
            'tombstones_view' => $tombstonesView,
            'deleted_view' => $deletedView,
            'full_path' => htmlspecialchars($this->rootDir),
            'date' => date('r'),
        ]);
        $this->dashboardTemplate->renderTo($this->reportDir.DIRECTORY_SEPARATOR.'dashboard.html');
    }

    private function renderTombstonesView(AnalyzerResult $result): string
    {
        $tombstonesView = '';
        foreach ($result->getPerFile() as $fileResult) {
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $itemList = $this->renderUndeadTombstones($fileResult);
                $itemList .= $this->renderDeadTombstones($fileResult);

                $fileName = PathNormalizer::makeRelativeTo($fileResult->getFile(), $this->rootDir);
                $tombstonesView .= $this->renderFile($fileName, $itemList);
            }
        }

        return $tombstonesView;
    }

    private function renderDeadTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getDead() as $tombstone) {
            $date = $tombstone->getTombstoneDate();
            $deadSince = '';
            if ($date) {
                if ($age = TimePeriodFormatter::formatAge($date)) {
                    $deadSince = 'for '.$age;
                } else {
                    $deadSince = 'since '.$date;
                }
            }
            $this->deadTemplate->setVar([
                'path_to_root' => './',
                'tombstone' => $this->linkTombstoneSource((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'method' => htmlspecialchars($tombstone->getMethod()),
                'dead_since' => $deadSince,
            ]);
            $itemList .= $this->deadTemplate->render();
        }

        return $itemList;
    }

    private function renderUndeadTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getUndead() as $tombstone) {
            $invocation = $this->renderInvokers($tombstone);
            $this->undeadTemplate->setVar([
                'path_to_root' => './',
                'tombstone' => $this->linkTombstoneSource((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'method' => htmlspecialchars($tombstone->getMethod()),
                'invocation' => $invocation,
            ]);
            $itemList .= $this->undeadTemplate->render();
        }

        return $itemList;
    }

    private function renderDeletedView(AnalyzerResult $result): string
    {
        $deletedView = '';
        foreach ($result->getPerFile() as $fileResult) {
            if ($fileResult->getDeletedCount()) {
                $fileName = PathNormalizer::makeRelativeTo($fileResult->getFile(), $this->rootDir);
                $deletedView .= $this->renderFile($fileName, $this->renderDeletedTombstones($fileResult));
            }
        }

        return $deletedView;
    }

    private function renderDeletedTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getDeleted() as $vampire) {
            $this->deletedTemplate->setVar([
                'path_to_root' => './',
                'tombstone' => htmlspecialchars((string) $vampire->getTombstone()),
                'line' => $vampire->getLine(),
                'method' => htmlspecialchars($vampire->getMethod()),
                'last_call' => TimePeriodFormatter::formatAge($vampire->getInvocationDate()),
            ]);
            $itemList .= $this->deletedTemplate->render();
        }

        return $itemList;
    }

    private function renderFile(string $fileName, string $itemList): string
    {
        $this->fileTemplate->setVar([
            'file' => $fileName,
            'item_list' => $itemList,
        ]);

        return $this->fileTemplate->render();
    }

    private function renderInvokers(Tombstone $tombstone): string
    {
        $invokers = [];
        foreach ($tombstone->getVampires() as $vampire) {
            $invokers[] = $vampire->getInvoker();
        }
        $invokers = array_unique($invokers);
        $invokersString = '';
        foreach ($invokers as $invoker) {
            $this->invokerTemplate->setVar([
                'invoker' => $invoker ? htmlspecialchars($invoker) : 'global scope',
            ]);
            $invokersString .= $this->invokerTemplate->render();
        }

        return $invokersString;
    }

    private function linkTombstoneSource(string $label, string $fileName, int $line): string
    {
        $relativePath = PathNormalizer::makeRelativeTo($fileName, $this->rootDir);

        return sprintf('<a href="./%s.html#%s">%s</a>', $relativePath, $line, htmlspecialchars($label));
    }
}
