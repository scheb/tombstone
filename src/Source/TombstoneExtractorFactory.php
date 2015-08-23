<?php
namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Scheb\Tombstone\Analyzer\TombstoneIndex;

class TombstoneExtractorFactory
{
    /**
     * @param TombstoneIndex $tombstoneIndex
     *
     * @return TombstoneExtractor
     */
    public static function create(TombstoneIndex $tombstoneIndex)
    {
        $visitor = new TombstoneVisitor($tombstoneIndex);
        $traverser = new NodeTraverser();
        $parser = new Parser(new Lexer());
        return new TombstoneExtractor($parser, $traverser, $visitor);
    }
}
