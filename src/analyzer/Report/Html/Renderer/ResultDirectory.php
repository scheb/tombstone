<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\ResultAggregateInterface;

class ResultDirectory implements ResultAggregateInterface
{
    /**
     * @var string[]
     */
    private $path;

    /**
     * @var ResultDirectory[]
     */
    private $directories = [];

    /**
     * @var AnalyzerFileResult[]
     */
    private $files = [];

    /**
     * @param string[] $path
     */
    public function __construct(array $path = [])
    {
        $this->path = $path;
    }

    /**
     * @return ResultDirectory[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return AnalyzerFileResult[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function getName(): string
    {
        return $this->path[\count($this->path) - 1];
    }

    public function getPath(): string
    {
        return implode('/', $this->path);
    }

    public function getDeadCount(): int
    {
        $count = 0;
        /** @var ResultAggregateInterface $element */
        foreach (array_merge($this->directories, $this->files) as $element) {
            $count += $element->getDeadCount();
        }

        return $count;
    }

    public function getUndeadCount(): int
    {
        $count = 0;
        /** @var ResultAggregateInterface $element */
        foreach (array_merge($this->directories, $this->files) as $element) {
            $count += $element->getUndeadCount();
        }

        return $count;
    }

    public function getDeletedCount(): int
    {
        $count = 0;
        /** @var ResultAggregateInterface $element */
        foreach (array_merge($this->directories, $this->files) as $element) {
            $count += $element->getDeletedCount();
        }

        return $count;
    }

    public function addFileResult(string $filePath, AnalyzerFileResult $fileResult): void
    {
        $firstSlash = strpos($filePath, '/');
        if (false === $firstSlash) {
            $this->files[$filePath] = $fileResult;

            return;
        }

        /** @var string $dirName */
        $dirName = substr($filePath, 0, $firstSlash);
        if (isset($this->directories[$dirName])) {
            $directory = $this->directories[$dirName];
        } else {
            $directory = new ResultDirectory(array_merge($this->path, [$dirName]));
            $this->directories[$dirName] = $directory;
        }

        $subPath = substr($filePath, $firstSlash + 1);
        $directory->addFileResult($subPath, $fileResult);
    }
}
