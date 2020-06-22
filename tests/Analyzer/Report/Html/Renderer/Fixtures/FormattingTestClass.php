<?php

namespace Scheb\Tombstone\Tests\Analyzer\Report\Html\Renderer\Fixtures;

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
