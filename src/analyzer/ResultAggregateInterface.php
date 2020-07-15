<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

interface ResultAggregateInterface
{
    public function getDeadCount(): int;

    public function getUndeadCount(): int;

    public function getDeletedCount(): int;
}
