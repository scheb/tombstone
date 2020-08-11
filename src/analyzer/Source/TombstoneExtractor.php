<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Error;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

class TombstoneExtractor implements TombstoneExtractorInterface
{
    /**
     * @var FilePathInterface|null
     */
    private $currentFile;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * @var TombstoneIndex
     */
    private $tombstoneIndex;

    public function __construct(Parser $parser, NodeTraverserInterface $traverser, TombstoneIndex $tombstoneIndex)
    {
        $this->parser = $parser;
        $this->traverser = $traverser;
        $this->tombstoneIndex = $tombstoneIndex;
    }

    public function extractTombstones(FilePathInterface $filePath): void
    {
        $this->currentFile = $filePath;
        $absoluteFilePath = $filePath->getAbsolutePath();
        if (!is_readable($absoluteFilePath)) {
            throw new TombstoneExtractionException(sprintf('File "%s" is not readable.', $absoluteFilePath));
        }

        try {
            $content = file_get_contents($absoluteFilePath);
            $stmts = $this->parser->parse($content);
            if (null === $stmts) {
                throw new TombstoneExtractionException(sprintf('PHP code in "%s" could not be parsed.', $absoluteFilePath));
            }

            // Calls back to onTombstoneFound()
            $this->traverser->traverse($stmts);
        } catch (TombstoneExtractionException $e) {
            throw $e;
        } catch (Error $e) {
            throw new TombstoneExtractionException(sprintf('PHP code in "%s" could not be parsed.', $absoluteFilePath), 0, $e);
        } catch (\Throwable $e) {
            throw new TombstoneExtractionException(sprintf('Exception while parsing "%s".', $absoluteFilePath), 0, $e);
        }

        $this->currentFile = null;
    }

    public function onTombstoneFound(string $functionName, array $arguments, int $line, ?string $method): void
    {
        if (null === $this->currentFile) {
            throw new \RuntimeException('Current file not available.');
        }

        $tombstone = new Tombstone($functionName, $arguments, $this->currentFile, $line, $method);
        $this->tombstoneIndex->addTombstone($tombstone);
    }
}
