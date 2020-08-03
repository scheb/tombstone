<?php

namespace Foo\Bar;

class FormattingTestClass
{
    /** PHPDoc */
    public function test(string $arg): void
    {
        // Comment
        echo 'foo'; ?>
        <p>Some inline HTML</p>
        <?php
    }
}
