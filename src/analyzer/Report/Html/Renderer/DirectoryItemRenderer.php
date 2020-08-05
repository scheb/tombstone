<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\ResultAggregateInterface;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use SebastianBergmann\Template\Template;

class DirectoryItemRenderer
{
    /**
     * @var Template|\Text_Template
     */
    private $directoryItemTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $barTemplate;

    public function __construct()
    {
        $this->directoryItemTemplate = TemplateProvider::getTemplate('directory_item.html');
        $this->barTemplate = TemplateProvider::getTemplate('percentage_bar.html');
    }

    public function renderDirectoryItem(string $name, string $link, ResultAggregateInterface $result, string $pathToRoot): string
    {
        $deadCount = $result->getDeadCount();
        $undeadCount = $result->getUndeadCount();
        $totalCount = $deadCount + $undeadCount;

        $this->directoryItemTemplate->setVar([
            'name' => htmlspecialchars($name),
            'path_to_root' => $pathToRoot,
            'icon' => $result instanceof AnalyzerFileResult ? 'code' : 'directory',
            'link' => $link,
            'class' => $this->getClass($undeadCount, $totalCount),
            'bar' => $this->renderBar($deadCount, $totalCount),
            'total' => $totalCount,
            'numDead' => $deadCount,
            'numUndead' => $undeadCount,
        ]);

        return $this->directoryItemTemplate->render();
    }

    private function getClass(int $undeadCount, int $totalCount): string
    {
        $class = 'success';
        if ($undeadCount > 0) {
            if ($undeadCount < $totalCount) {
                $class = 'warning';
            } else {
                $class = 'danger';
            }
        }

        return $class;
    }

    private function renderBar(int $numDead, int $total): string
    {
        $this->barTemplate->setVar([
            'level' => 'success',
            'percent' => round($numDead / $total * 100, 2),
        ]);

        return $this->barTemplate->render();
    }
}
