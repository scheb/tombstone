<?php

declare(strict_types=1);

namespace Scheb\Tombstone\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Logger\Graveyard\VampireFactory;
use Scheb\Tombstone\Logger\Tracing\TraceProvider;

class LogProvider implements LogReaderInterface
{
    /**
     * @var VampireFactory
     */
    private $vampireFactory;

    public function __construct()
    {
        $this->vampireFactory = new VampireFactory(new RootPath(__DIR__.'/..'), 3);
    }

    public function iterateVampires(): \Traversable
    {
        $trace = TraceProvider::getTraceHere(0);
        yield $this->vampireFactory->createFromCall(['customProvided'], $trace, []);
    }
}
