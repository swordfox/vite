<?php

namespace Swordfox\Vite\Helpers;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\View\ViewableData;
use SilverStripe\Core\Environment;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;

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

            if ($this->manifest($path)) {

                return '<link rel="modulepreload" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" /><script type="module" src="' . $this->base . '/build/' . $this->manifest($path)['file'] . '"></script>';
            }
        }
    }

    public function CSS($path = 'themes/custom/src/app.scss')
    {
        if (self::hotAsset()) {
            
            return '<link rel="stylesheet" href="' . self::hotAsset($path) . '" />';
        } else {

            if ($this->manifest($path)) {
                
                return '<link rel="preload" as="style" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" /><link rel="stylesheet" href="' . $this->base . '/build/' . $this->manifest($path)['file'] . '" />';
            }
        }
    }

    public static function asset($path)
    {
        return self::assetLink($path);
    }

    public static function image($path)
    {
        $config = Config::inst()->get('Swordfox\Vite');

        if (isset($config['assetsImageFolder'])) {

            $dir = $config['assetsImageFolder'];

            if (substr($dir, -1) == '/') {
                $dir = substr($dir, 0, strlen($dir) - 1);
            }

            return self::asset($dir . '/' . $path);

        } else {

            return self::asset($path);

        }
    }

    public static function assetLink($path, $forceBuild = false)
    {
        if (self::hotAsset() && !$forceBuild) {
            
            return self::hotAsset($path);
        } else {

            if (self::manifest($path)) {

                return 'build/' . self::manifest($path)['file'];
            } else {

                return null;
            }
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

    public static function adminAssets()
    {
        $config = Config::inst()->get('Swordfox\Vite');

        if (isset($config['extra_requirements_css'])) {
            $reqs = LeftAndMain::config()->get('extra_requirements_css');

            foreach($config['extra_requirements_css'] as $req) {

                $r = self::assetLink($req);

                if ($r) {
                    $reqs[] = self::assetLink($req);
                }
            }

            LeftAndMain::config()->set('extra_requirements_css', $reqs);

            if (self::hotAsset()) {
                // LeftAndMain::config()->set('extra_requirements_javascript', [
                //   Vite::assetLink('@vite/client')
                // ]);
                Requirements::insertHeadTags('<script type="module" src="@vite/client"></script>');
            }
        }

        if (isset($config['editor_css'])) {
            $reqs = [];

            foreach($config['editor_css'] as $req) {
                $r = self::assetLink($req, true);

                if ($r) {
                    $reqs[] = self::assetLink($req);
                }
            }

            TinyMCEConfig::config()->set('editor_css', $reqs);
        }
    }
}