<?php
namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use Scheb\Tombstone\Analyzer\Exception\TombstoneExtractionException;
use Scheb\Tombstone\Analyzer\TombstoneIndex;

class TombstoneExtractor
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

    /**
     * @param Parser $parser
     * @param NodeTraverser $traverser
     * @param TombstoneVisitor $visitor
     */
    public function __construct(Parser $parser, NodeTraverser $traverser, TombstoneVisitor $visitor)
    {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->traverser = $traverser;
        $this->traverser->addVisitor($visitor);
    }

    /**
     * @param string $filePath
     *
     * @throws TombstoneExtractionException
     */
    public function extractTombstones($filePath) {
        $this->visitor->setCurrentFile($filePath);
        if (!is_readable($filePath)) {
            throw new TombstoneExtractionException('File "' . $filePath . '" is not readable.');
        }

        try {
            $code = file_get_contents($filePath);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        } catch (Error $e) {
            throw new TombstoneExtractionException('PHP code in "' . $filePath . '" could not be parsed.', null, $e);
        }
    }

    /**
     * @return TombstoneIndex
     */
    public function getTombstones()
    {
        return $this->visitor->getTombstones();
    }
}
