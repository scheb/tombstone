<?php

declare(strict_types=1);

namespace Foo;

class Class1
{
    public static function staticMethod(): void
    {
        tombstone('2020-01-01', 'Class1');
    }
}
