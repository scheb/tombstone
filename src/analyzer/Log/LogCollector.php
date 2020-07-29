<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Model\VampireIndex;

class LogCollector
{
    /**
     * @var LogReaderInterface[]
     */
    private $logReaders;

    /**
     * @var VampireIndex
     */
    private $vampireIndex;

    public function __construct(array $logReaders, VampireIndex $vampireIndex)
    {
        $this->logReaders = $logReaders;
        $this->vampireIndex = $vampireIndex;
    }

    public function collectLogs(): void
    {
        foreach ($this->logReaders as $logReader) {
            foreach ($logReader->iterateVampires() as $vampire) {
                $this->vampireIndex->addVampire($vampire);
            }
        }
    }
}
