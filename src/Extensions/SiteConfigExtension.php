<?php

namespace Swordfox\Vite\Extensions;

use SilverStripe\ORM\DataExtension;
use Swordfox\Vite\Helpers\Vite;

class SiteConfigExtension extends DataExtension
{
    public function Vite()
    {
        return Vite::create();
    }
}
