<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Scheb\Tombstone\Analyzer\Exception\TombstoneExtractionException;

class TombstoneExtractor implements TombstoneExtractorInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var TombstoneVisitor
     */
    private $visitor;

    public function __construct(Parser $parser, NodeTraverser $traverser, TombstoneVisitor $visitor)
    {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->traverser = $traverser;
        $this->traverser->addVisitor($visitor);
    }

    public function extractTombstones(string $filePath): void
    {
        $this->visitor->setCurrentFile($filePath);
        if (!is_readable($filePath)) {
            throw new TombstoneExtractionException(sprintf('File "%s" is not readable.', $filePath));
        }

        try {
            $code = file_get_contents($filePath);
            $stmts = $this->parser->parse($code);
            if (null === $stmts) {
                throw new TombstoneExtractionException(sprintf('PHP code in "%s" could not be parsed.', $filePath));
            }

            $this->traverser->traverse($stmts);
        } catch (Error $e) {
            throw new TombstoneExtractionException(sprintf('PHP code in "%s" could not be parsed.', $filePath), 0, $e);
        }
    }
}
