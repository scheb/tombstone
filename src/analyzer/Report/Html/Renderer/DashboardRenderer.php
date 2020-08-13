<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Analyzer\Report\TimePeriodFormatter;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use SebastianBergmann\Template\Template;

class DashboardRenderer
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var Template|\Text_Template
     */
    private $dashboardTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $fileTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $deadTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $undeadTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $deletedTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $invokerTemplate;

    public function __construct(string $reportDir)
    {
        $this->reportDir = $reportDir;
        $this->dashboardTemplate = TemplateProvider::getTemplate('dashboard.html');
        $this->fileTemplate = TemplateProvider::getTemplate('dashboard_file.html');
        $this->deadTemplate = TemplateProvider::getTemplate('dashboard_dead.html');
        $this->undeadTemplate = TemplateProvider::getTemplate('dashboard_undead.html');
        $this->deletedTemplate = TemplateProvider::getTemplate('dashboard_deleted.html');
        $this->invokerTemplate = TemplateProvider::getTemplate('dashboard_invoker.html');
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
            'path_to_root' => '',
            'date' => date('r'),
            'tombstones_count' => $total,
            'dead_count' => $numDead,
            'undead_count' => $numUndead,
            'deleted_count' => $numDeleted,
            'dead_percent' => $deadPercent,
            'undead_percent' => $undeadPercent,
            'tombstones_view' => $tombstonesView,
            'deleted_view' => $deletedView,
        ]);
        $this->dashboardTemplate->renderTo(FileSystem::createPath($this->reportDir, 'dashboard.html'));
    }

    private function renderTombstonesView(AnalyzerResult $result): string
    {
        $tombstonesView = '';
        foreach ($result->getFileResults() as $fileResult) {
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $itemList = $this->renderUndeadTombstones($fileResult);
                $itemList .= $this->renderDeadTombstones($fileResult);

                $fileName = $fileResult->getFile()->getReferencePath();
                $tombstonesView .= $this->renderFile($fileName, $itemList);
            }
        }

        return $tombstonesView;
    }

    private function renderDeadTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getDead() as $tombstone) {
            $this->deadTemplate->setVar([
                'path_to_root' => '',
                'tombstone' => $this->linkToTombstoneInCode((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'scope' => $this->getTombstoneScope($tombstone),
                'dead_since' => $this->getDeadSince($tombstone),
            ]);
            $itemList .= $this->deadTemplate->render();
        }

        return $itemList;
    }

    private function getDeadSince(Tombstone $tombstone): string
    {
        $date = $tombstone->getTombstoneDate();
        if (null === $date) {
            return 'since unknown';
        }

        if ($age = TimePeriodFormatter::formatAge($date)) {
            return 'for '.$age;
        }

        return 'since '.$date;
    }

    private function renderUndeadTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getUndead() as $tombstone) {
            $this->undeadTemplate->setVar([
                'path_to_root' => '',
                'tombstone' => $this->linkToTombstoneInCode((string) $tombstone, $fileResult->getFile(), $tombstone->getLine()),
                'line' => $tombstone->getLine(),
                'scope' => $this->getTombstoneScope($tombstone),
                'invocation' => $this->renderInvokers($tombstone),
            ]);
            $itemList .= $this->undeadTemplate->render();
        }

        return $itemList;
    }

    private function renderInvokers(Tombstone $tombstone): string
    {
        $invokers = [];
        foreach ($tombstone->getVampires() as $vampire) {
            $invokers[] = $vampire->getInvoker();
        }

        $invokers = array_unique($invokers);
        sort($invokers);
        $invokersString = '';
        foreach ($invokers as $invoker) {
            $this->invokerTemplate->setVar([
                'invoker' => $invoker ? htmlspecialchars($invoker) : 'global scope',
            ]);
            $invokersString .= $this->invokerTemplate->render();
        }

        return $invokersString;
    }

    private function renderDeletedView(AnalyzerResult $result): string
    {
        $deletedView = '';
        foreach ($result->getFileResults() as $fileResult) {
            if ($fileResult->getDeletedCount()) {
                $fileName = $fileResult->getFile()->getReferencePath();
                $deletedView .= $this->renderFile($fileName, $this->renderDeletedTombstones($fileResult));
            }
        }

        return $deletedView;
    }

    private function renderFile(string $fileName, string $itemList): string
    {
        $this->fileTemplate->setVar([
            'file' => $fileName,
            'item_list' => $itemList,
        ]);

        return $this->fileTemplate->render();
    }

    private function renderDeletedTombstones(AnalyzerFileResult $fileResult): string
    {
        $itemList = '';
        foreach ($fileResult->getDeleted() as $vampire) {
            $this->deletedTemplate->setVar([
                'path_to_root' => './',
                'tombstone' => htmlspecialchars((string) $vampire->getTombstone()),
                'line' => $vampire->getLine(),
                'scope' => $this->getTombstoneScope($vampire->getTombstone()),
                'last_call' => $this->getLastCalled($vampire),
            ]);
            $itemList .= $this->deletedTemplate->render();
        }

        return $itemList;
    }

    private function getLastCalled(Vampire $vampire): string
    {
        $invocationDate = $vampire->getInvocationDate();
        if ($age = TimePeriodFormatter::formatAge($invocationDate)) {
            return $age;
        }

        return 'unknown';
    }

    private function getTombstoneScope(Tombstone $tombstone): string
    {
        if ($tombstone->getMethod()) {
            return sprintf('method <samp>%s</samp>', htmlspecialchars($tombstone->getMethod()));
        }

        return 'global scope';
    }

    private function linkToTombstoneInCode(string $label, FilePathInterface $file, int $line): string
    {
        if ($file instanceof RelativeFilePath) {
            return sprintf('<a href="./%s.html#%s">%s</a>', $file->getRelativePath(), $line, htmlspecialchars($label));
        }

        return htmlspecialchars($label);
    }
}
