<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

class AnalyzerDirectoryResult implements ResultAggregateInterface
{
    /**
     * Empty string for the root directory.
     *
     * @var string
     */
    private $directoryPath;

    /**
     * @var int|null
     */
    private $deadCount;

    /**
     * @var int|null
     */
    private $undeadCount;

    /**
     * @var int|null
     */
    private $deletedCount;

    /**
     * @var AnalyzerDirectoryResult[]
     */
    private $subDirectoryResults = [];

    /**
     * @var AnalyzerFileResult[]
     */
    private $fileResults = [];

    /**
     * @param AnalyzerDirectoryResult[] $subDirectoryResults
     * @param AnalyzerFileResult[] $fileResults
     */
    public function __construct(string $directoryPath, array $subDirectoryResults, array $fileResults)
    {
        $this->directoryPath = $directoryPath;
        $this->subDirectoryResults = $subDirectoryResults;
        $this->fileResults = $fileResults;
    }

    /**
     * @return AnalyzerDirectoryResult[]
     */
    public function getSubDirectoryResults(): array
    {
        return $this->subDirectoryResults;
    }

    /**
     * @return AnalyzerFileResult[]
     */
    public function getFileResults(): array
    {
        return $this->fileResults;
    }

    public function getDirectoryPath(): string
    {
        return $this->directoryPath;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function getDeadCount(): int
    {
        if (null === $this->deadCount) {
            $this->sumUpCounts();
        }

        return $this->deadCount;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function getUndeadCount(): int
    {
        if (null === $this->deadCount) {
            $this->sumUpCounts();
        }

        return $this->undeadCount;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function getDeletedCount(): int
    {
        if (null === $this->deadCount) {
            $this->sumUpCounts();
        }

        return $this->deletedCount;
    }

    private function sumUpCounts(): void
    {
        $this->deadCount = 0;
        $this->undeadCount = 0;
        $this->deletedCount = 0;

        foreach ($this->subDirectoryResults as $subDirectoryResult) {
            $this->deadCount += $subDirectoryResult->getDeadCount();
            $this->undeadCount += $subDirectoryResult->getUndeadCount();
            $this->deletedCount += $subDirectoryResult->getDeletedCount();
        }

        foreach ($this->fileResults as $fileResult) {
            $this->deadCount += $fileResult->getDeadCount();
            $this->undeadCount += $fileResult->getUndeadCount();
            $this->deletedCount += $fileResult->getDeletedCount();
        }
    }
}
