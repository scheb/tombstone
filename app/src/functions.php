<?php
declare(strict_types=1);

//phpcs:disable Squiz.Functions.GlobalFunction.Found

// Another tombstone function with a different name
use Scheb\Tombstone\Logger\Graveyard\GraveyardRegistry;
use Scheb\Tombstone\Logger\Tracing\TraceProvider;

// An alternative tombstone function with a different name
function deadCodeDetection(...$arguments): void
{
    $trace = TraceProvider::getTraceHere();
    GraveyardRegistry::getGraveyard()->logTombstoneCall($arguments, $trace, []);
}

function deadCodeFunction(): void
{
    if (false) {
        // This should be detected as dead
        tombstone('2015-01-01', 'author', 'deadCodeFunction_if');
    }

    // These should be detected as vampires
    tombstone('2015-01-01', 'author', 'deadCodeFunction');
    tombstone('2015-01-01', 'author');
    deadCodeDetection('2020-12-29', 'author');
}
