<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Model\VampireIndex;

class LogCollector
{
    /**
     * @var LogProviderInterface[]
     */
    private $logProviders;

    /**
     * @var VampireIndex
     */
    private $vampireIndex;

    public function __construct(array $logProviders, VampireIndex $vampireIndex)
    {
        $this->logProviders = $logProviders;
        $this->vampireIndex = $vampireIndex;
    }

    public function collectLogs(): void
    {
        foreach ($this->logProviders as $logProvider) {
            foreach ($logProvider->getVampires() as $vampire) {
                $this->vampireIndex->addVampire($vampire);
            }
        }
    }
}
