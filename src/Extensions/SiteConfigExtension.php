<?php

namespace Swordfox\Vite\Extensions;

use Swordfox\Vite\Helpers\Vite;
use SilverStripe\Core\Extension;

class SiteConfigExtension extends Extension
{
    public function Vite()
    {
        return Vite::create();
    }
}
