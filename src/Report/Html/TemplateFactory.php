<?php

namespace Scheb\Tombstone\Analyzer\Report\Html;

class TemplateFactory
{
    public static function getTemplate(string $file): \Text_Template
    {
        return new \Text_Template(__DIR__.'/Template/'.$file, '{{', '}}');
    }
}
