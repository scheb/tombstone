<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Handler;

use Redis;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Logger\Formatter\FormatterInterface;

class AnalyzerRedisHandler extends AbstractHandler
{
    /**
     * @var Redis
     */
    private $client;
    /**
     * @var int
     */
    private $sizeLimit;

    public function __construct(Redis $client, int $sizeLimit = 1000)
    {
        $this->client = $client;
        $this->sizeLimit = $sizeLimit;
    }

    public function log(Vampire $vampire): void
    {
        $this->client->xAdd(
            $this->getLogKey($vampire),
            '*',
            ['data' => $this->getFormatter()->format($vampire)],
            $this->sizeLimit,
            true
        );
    }

    private function getLogKey(Vampire $vampire): string
    {
        $date = date('Ymd');
        $hash = $vampire->getTombstone()->getHash();

        return sprintf('%s-%s.tombstone', $hash, $date);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new AnalyzerLogFormatter();
    }
}