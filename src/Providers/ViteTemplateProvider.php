<?php

namespace Swordfox\Vite\Providers;

use SilverStripe\View\TemplateGlobalProvider;
use Swordfox\Vite\Helpers\Vite;

class ViteTemplateProvider implements TemplateGlobalProvider
{
    /**
     * @return array|void
     */
    public static function get_template_global_variables(): array
    {
        return [
            'Vite',
        ];
    }

    /**
     * @return boolean
     */
    public static function Vite() : Vite
    {
        return Vite::create();
    }
}
