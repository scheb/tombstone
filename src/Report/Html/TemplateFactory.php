<?php
namespace Scheb\Tombstone\Analyzer\Report\Html;

class TemplateFactory
{
    /**
     * @param string $file
     *
     * @return \Text_Template
     */
    public static function getTemplate($file) {
        return new \Text_Template(__DIR__ . '/Template/' . $file, '{{', '}}');
    }
}
