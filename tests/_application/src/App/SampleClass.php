<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\TestApplication\App;

class SampleClass
{
    public function deadCodeMethod(): void
    {
        if (false) {
            // This should be detected as dead
            tombstone('2015-01-01', 'author', 'deadCodeMethod_if');
        }

        // These should be detected as vampires
        tombstone('2015-01-01', 'author', 'deadCodeMethod');
        tombstone('2015-01-01', 'author');
    }
}
