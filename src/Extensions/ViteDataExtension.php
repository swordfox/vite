<?php

namespace Swordfox\Vite\Extensions;

use SilverStripe\ORM\DataExtension;
use Swordfox\Vite\Helpers\Vite;

class ViteDataExtension extends DataExtension
{
    public function getVite() : Vite
    {
        return Vite::create();
    }
}