<?php

function deadCodeFunction(): void
{
    if (false) {
        // This should be detected as dead
        tombstone('2015-01-01', 'author', 'deadCodeFunction_if');
    }

    // These should be detected as vampires
    tombstone('2015-01-01', 'author', 'deadCodeFunction');
    tombstone('2015-01-01', 'author');
}
