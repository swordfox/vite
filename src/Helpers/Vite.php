<?php

namespace Swordfox\Vite\Helpers;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\View\ViewableData;
use SilverStripe\Core\Environment;

class Vite extends ViewableData
{
    use Configurable;

    /**
     * The path to the "hot" file.
     *
     * @var string|null
     */
    public static $hotFile = './hot';

    public static $base;

    public function __construct()
    {
        parent::__construct();
        $config = self::config();

        $this->base = Environment::getEnv('SS_BASE_URL');
    }

    public function JS($path = 'themes/custom/src/app.js')
    {
        if (self::hotAsset()) {

            return '<script type="module" src="' . self::hotAsset('@vite/client') . '"></script>
            <script type="module" src="' . self::hotAsset($path) . '"></script>
            <script type="module" src="' . self::hotAsset('@vite/client') . '"></script>';
        } else {

            return '<link rel="modulepreload" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" /><script type="module" src="' . $this->base . '/build/' . $this->manifest($path)['file'] . '"></script>';
        }
    }

    public function CSS($path = 'themes/custom/src/app.scss')
    {
        if (self::hotAsset()) {
            
            return '<link rel="stylesheet" href="' . self::hotAsset($path) . '" />';
        } else {

            return '<link rel="preload" as="style" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" /><link rel="stylesheet" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" />';
        }
    }

    public static function assetLink($path)
    {
        if (self::hotAsset()) {
            
            return self::hotAsset($path);
        } else {

            return 'build/' . self::manifest($path)['file'];
        }
    }

    /**
     * Get the path to a given asset when running in HMR mode.
     *
     * @return string
     */
    public static function hotAsset($asset = null)
    {
        if (file_exists(self::$hotFile)) {
            
            return rtrim(file_get_contents(self::$hotFile)) . ($asset ? ('/' . $asset) : '');
        } else {

            return null;
        }
    }

    /**
     * Get the the manifest file for the given build directory.
     *
     * @return array
     */
    public static function manifest($path = null)
    {
        $manifest = null;

        $manifestPath = './build/manifest.json';

        if (is_file($manifestPath)) {

            $manifest = json_decode(file_get_contents($manifestPath), true);

            if ($path) {
                $manifest = $manifest[$path];
            }
        }

        return $manifest;
    }
}