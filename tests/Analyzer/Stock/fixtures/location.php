<?php

// phpcs:ignoreFile

namespace {
    tombstone('2020-01-01', 'global statement');

    function globalFunction(): void
    {
        tombstone('2020-01-01', 'global function');
    }
}

namespace Foo {
    tombstone('2020-01-01', 'namespaced statement');

    class Bar
    {
        public function method(): void
        {
            tombstone('2020-01-01', 'class method');
        }

        public static function staticFunction(): void
        {
            tombstone('2020-01-01', 'class method');
        }
    }
}
