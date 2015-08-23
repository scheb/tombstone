<?php
namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Scheb\Tombstone\Analyzer\TombstoneList;

class TombstoneExtractorFactory
{
    /**
     * @return TombstoneExtractor
     */
    public static function create()
    {
        $tombstoneList = new TombstoneList();
        $visitor = new TombstoneVisitor($tombstoneList);
        $traverser = new NodeTraverser();
        $parser = new Parser(new Lexer());
        return new TombstoneExtractor($parser, $traverser, $visitor);
    }
}
