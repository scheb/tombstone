<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Stock;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Model\RootPath;
use SebastianBergmann\FinderFacade\FinderFacade;

class ParserTombstoneProvider implements TombstoneProviderInterface
{
    /**
     * @var FinderFacade
     */
    private $fileFinder;

    /**
     * @var TombstoneExtractor
     */
    private $tombstoneExtractor;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    public function __construct(FinderFacade $fileFinder, TombstoneExtractor $tombstoneExtractor, ConsoleOutputInterface $output)
    {
        $this->fileFinder = $fileFinder;
        $this->tombstoneExtractor = $tombstoneExtractor;
        $this->output = $output;
    }

    public static function create(array $config, ConsoleOutputInterface $consoleOutput): TombstoneProviderInterface
    {
        $sourceRootPath = new RootPath($config['source_code']['root_directory']);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());
        $traverser = new NodeTraverser();
        $extractor = new TombstoneExtractor($parser, $traverser, $sourceRootPath);
        $traverser->addVisitor(new TombstoneNodeVisitor($extractor, $config['tombstones']['parser']['function_names']));

        $finder = new FinderFacade(
            [$config['source_code']['root_directory']],
            $config['tombstones']['parser']['excludes'],
            $config['tombstones']['parser']['names'],
            $config['tombstones']['parser']['not_names']
        );

        return new self($finder, $extractor, $consoleOutput);
    }

    public function getTombstones(): iterable
    {
        $this->output->writeln('Parse tombstones from source code ...');
        $files = $this->fileFinder->findFiles();
        $progress = $this->output->createProgressBar(\count($files));

        foreach ($files as $file) {
            $this->output->debug($file);
            $tombstones = $this->tombstoneExtractor->extractTombstones($file);
            foreach ($tombstones as $tombstone) {
                yield $tombstone;
            }
            $progress->advance();
        }

        $this->output->writeln();
    }
}
