<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\Model\Tombstone;
use SebastianBergmann\Template\Template;

class FileTombstoneListRenderer
{
    /**
     * @var Template|\Text_Template
     */
    private $tombstoneTemplate;

    public function __construct()
    {
        $this->tombstoneTemplate = TemplateProvider::getTemplate('file_tombstone.html');
    }

    public function renderTombstonesList(AnalyzerFileResult $fileResult): string
    {
        $tombstoneList = [];

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

    private function renderTombstoneItem(Tombstone $tombstone, string $class): string
    {
        $this->tombstoneTemplate->setVar([
            'tombstone' => htmlspecialchars((string) $tombstone),
            'line' => $tombstone->getLine(),
            'method' => htmlspecialchars($tombstone->getMethod() ?? ''),
            'level' => $class,
        ]);

        return $this->tombstoneTemplate->render();
    }
}
