<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\TombstoneIndex;

class TombstoneExtractorFactory implements TombstoneExtractorFactoryInterface
{
    public static function create(array $config, TombstoneIndex $tombstoneIndex, ConsoleOutput $output): TombstoneExtractorInterface
    {
        $visitor = new TombstoneVisitor($tombstoneIndex);
        $traverser = new NodeTraverser();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());

        return new TombstoneExtractor($parser, $traverser, $visitor);
    }
}
