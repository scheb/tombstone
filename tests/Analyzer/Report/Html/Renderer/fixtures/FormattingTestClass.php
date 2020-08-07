<?php

namespace Foo\Bar;

class FormattingTestClass
{
    /** PHPDoc */
    public function test(string $arg): void
    {
        // Comment
        echo 'foo'."bar"; ?>
        <p>Some inline HTML</p>
        <?php
        echo <<<CODE
Hello world!
CODE;
    }
}
