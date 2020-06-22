<?php

namespace Scheb\Tombstone\Analyzer;

interface ResultAggregateInterface
{
    public function getDeadCount(): int;

    public function getUndeadCount(): int;

    public function getDeletedCount(): int;
}
