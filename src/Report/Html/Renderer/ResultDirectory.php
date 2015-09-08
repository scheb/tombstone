<?php
namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;

class ResultDirectory
{
    /**
     * @var string[]
     */
    private $path;

    /**
     * @var ResultDirectory[]
     */
    private $directories = array();

    /**
     * @var AnalyzerFileResult[]
     */
    private $files = array();

    /**
     * @param string[] $path
     */
    public function __construct($path = array())
    {
        $this->path = $path;
    }

    /**
     * @return ResultDirectory[]
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @return AnalyzerFileResult[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->path[count($this->path) - 1];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return implode('/', $this->path);
    }

    /**
     * @return int
     */
    public function getDeadCount()
    {
        $count = 0;
        /** @var ResultDirectory|AnalyzerFileResult $element */
        foreach (array_merge($this->directories, $this->files) as $element) {
            $count += $element->getDeadCount();
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getUndeadCount()
    {
        $count = 0;
        /** @var ResultDirectory|AnalyzerFileResult $element */
        foreach (array_merge($this->directories, $this->files) as $element) {
            $count += $element->getUndeadCount();
        }

        return $count;
    }

    /**
     * @param $filePath
     * @param AnalyzerFileResult $fileResult
     */
    public function addFileResult($filePath, AnalyzerFileResult $fileResult) {
        $firstSlash = strpos($filePath, '/');
        if ($firstSlash === false) {
            $this->files[$filePath] = $fileResult;
            return;
        }

        $dirName = substr($filePath, 0, $firstSlash);
        if (isset($this->directories[$dirName])) {
            $directory = $this->directories[$dirName];
        } else {
            $directory = new ResultDirectory(array_merge($this->path, array($dirName)));
            $this->directories[$dirName] = $directory;
        }

        $subPath = substr($filePath, $firstSlash + 1);
        $directory->addFileResult($subPath, $fileResult);
    }
}
