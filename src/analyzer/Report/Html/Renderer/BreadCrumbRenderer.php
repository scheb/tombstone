<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\PathNormalizer;
use SebastianBergmann\Template\Template;

class BreadCrumbRenderer
{
    /**
     * @var string
     */
    private $sourceRootPath;

    /**
     * @var Template|\Text_Template
     */
    private $itemTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $itemActiveTemplate;

    public function __construct(RootPath $sourceRootPath)
    {
        $this->sourceRootPath = rtrim($sourceRootPath->getAbsolutePath(), '\\/');
        $this->itemTemplate = TemplateProvider::getTemplate('breadcrumb_item.html');
        $this->itemActiveTemplate = TemplateProvider::getTemplate('breadcrumb_item_active.html');
    }

    public function renderBreadcrumbRoot(): string
    {
        return $this->renderItem($this->sourceRootPath, 'index.html');
    }

    public function renderBreadcrumbToFile(string $relativeFilePath): string
    {
        return $this->renderBreadcrumb($relativeFilePath, true);
    }

    public function renderBreadcrumbToDirectory(string $relativeDirectoryPath): string
    {
        return $this->renderBreadcrumb($relativeDirectoryPath, false);
    }

    public function renderBreadcrumb(string $relativeFilePath, bool $isFile): string
    {
        if ('' === $relativeFilePath) {
            return $this->renderActiveItem($this->sourceRootPath);
        }

        $pathSegments = explode(PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR, $relativeFilePath);
        $directoryLevel = \count($pathSegments) - ($isFile ? 1 : 0);
        $breadcrumbString = $this->renderItem($this->sourceRootPath, str_repeat('../', $directoryLevel).'index.html');

        while ($label = array_shift($pathSegments)) {
            --$directoryLevel;
            if (0 === \count($pathSegments)) {
                $breadcrumbString .= $this->renderActiveItem($label);
            } else {
                $link = str_repeat('../', $directoryLevel).'index.html';
                $breadcrumbString .= $this->renderItem($label, $link);
            }
        }

        return $breadcrumbString;
    }

    private function renderItem(string $label, string $link): string
    {
        $this->itemTemplate->setVar([
            'label' => htmlspecialchars($label),
            'link' => htmlspecialchars($link),
        ]);

        return $this->itemTemplate->render();
    }

    private function renderActiveItem(string $label): string
    {
        $this->itemActiveTemplate->setVar([
            'label' => htmlspecialchars($label),
        ]);

        return $this->itemActiveTemplate->render();
    }
}
