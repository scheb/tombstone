<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\TestApplication\App;

class DeletedTombstoneClass
{
    public function invokeDeletedTombstone()
    {
        // This invokes a tombstone that isn't detected by code analysis, therefore evaluated as "deleted"
        eval('tombstone("deleted");');
    }
}
