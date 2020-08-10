<?php

declare(strict_types=1);

namespace Foo\Bar;

class Class2
{
    public function publicMethod(): void
    {
        tombstone('2020-01-01', 'Class2');
    }
}
