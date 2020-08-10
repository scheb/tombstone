<?php

declare(strict_types=1);

namespace Foo\Bar;

class Class3
{
    public function someOtherMethod(): void
    {
        tombstone('2020-01-01', 'Class3');
    }
}
