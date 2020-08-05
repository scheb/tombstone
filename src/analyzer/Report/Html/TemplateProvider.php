<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html;

use SebastianBergmann\Template\Template;

class TemplateProvider
{
    /**
     * @return Template|\Text_Template
     */
    public static function getTemplate(string $file)
    {
        if (class_exists(Template::class)) {
            return new Template(__DIR__.'/Template/'.$file, '{{', '}}');
        }

        return new \Text_Template(__DIR__.'/Template/'.$file, '{{', '}}');
    }
}
