<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Stock;

use PhpParser\Error;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Tombstone;

class TombstoneExtractor
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * @var RootPath
     */
    private $sourceRootPath;

    /**
     * @var string|null
     */
    private $currentFilePath;

    /**
     * @var Tombstone[]
     */
    private $extractedTombstones = [];

    public function __construct(Parser $parser, NodeTraverserInterface $traverser, RootPath $sourceRootPath)
    {
        $this->parser = $parser;
        $this->traverser = $traverser;
        $this->sourceRootPath = $sourceRootPath;
    }

    public function extractTombstones(string $filePath): array
    {
        try {
            $this->extractedTombstones = [];
            $this->currentFilePath = $filePath;
            if (!is_readable($filePath)) {
                throw new TombstoneExtractorException(sprintf('File "%s" is not readable.', $filePath));
            }

            $this->parseSourceCode($filePath);

            return $this->extractedTombstones;
        } finally {
            $this->currentFilePath = null;
            $this->extractedTombstones = [];
        }
    }

    private function parseSourceCode(string $absoluteFilePath): void
    {
        try {
            $content = file_get_contents($absoluteFilePath);
            $stmts = $this->parser->parse($content);
            if (null === $stmts) {
                throw new TombstoneExtractorException(sprintf('PHP code in "%s" could not be parsed.', $absoluteFilePath));
            }

            // Calls back to onTombstoneFound()
            $this->traverser->traverse($stmts);
        } catch (TombstoneExtractorException $e) {
            throw $e;
        } catch (Error $e) {
            throw new TombstoneExtractorException(sprintf('PHP code in "%s" could not be parsed.', $absoluteFilePath), 0, $e);
        } catch (\Throwable $e) {
            throw new TombstoneExtractorException(sprintf('Exception while parsing "%s".', $absoluteFilePath), 0, $e);
        }
    }

    public function onTombstoneFound(string $functionName, array $arguments, int $line, ?string $method): void
    {
        if (null === $this->currentFilePath) {
            throw new \RuntimeException('Current file not available.');
        }

        $filePath = $this->sourceRootPath->createFilePath($this->currentFilePath);
        $this->extractedTombstones[] = new Tombstone($functionName, $arguments, $filePath, $line, $method);
    }
}
