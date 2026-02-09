<?php

namespace Swordfox\Vite\Helpers;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Environment;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\TinyMCE\TinyMCEConfig;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use SilverStripe\Model\ModelData;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\SSViewer;

class Vite extends ModelData
{
    use Configurable;

    /**
     * The path to the "hot" file.
     *
     * @var string|null
     */
    private static $hotFile = './hot';

    public function __construct()
    {
        parent::__construct();

        $config = self::config();
    }

    public function JS($path = null)
    {
        if (!$path) {

            $themes = SSViewer::get_themes();
            $path = 'themes/'.$themes[1].'/src/app.js';
        }

        if (self::hotAsset()) {

            return $this->writeHTML('<script type="module" src="' . self::hotAsset('@vite/client') . '"></script>
            <script type="module" src="' . self::hotAsset($path) . '"></script>');
        } else {

            if ($this->manifest($path)) {

                return $this->writeHTML('<link rel="modulepreload" href="' . $this->getBase() . '/' . self::buildPath() . '/' . $this->manifest($path)['file'] . '" /><script type="module" src="' . $this->getBase() . '/' . self::buildPath() . '/' . $this->manifest($path)['file'] . '"></script>');
            }
        }
    }

    public function CSS($path = null)
    {
        if (!$path) {

            $themes = SSViewer::get_themes();
            $path = 'themes/'.$themes[1].'/src/app.scss';
        }

        if (self::hotAsset()) {

            return $this->writeHTML('<script type="module" src="' . self::hotAsset('@vite/client') . '"></script>
            <link rel="stylesheet" href="' . self::hotAsset($path) . '" />');
        } else {

            if ($this->manifest($path)) {

                return $this->writeHTML('<link rel="preload" as="style" href="' . $this->getBase() . '/' . self::buildPath() . '/' . $this->manifest($path)['file'] . '" /><link rel="stylesheet" href="' . $this->getBase() . '/' . self::buildPath() . '/' . $this->manifest($path)['file'] . '" />');
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

            return Environment::getEnv('APP_URL_CDN') . self::asset($path);

        }
    }

    public static function assetLink($path, $forceBuild = false)
    {
        if (self::hotAsset() && !$forceBuild) {

            return self::hotAsset($path);
        } else {

            if (self::manifest($path)) {

                return Environment::getEnv('APP_URL_CDN') . '/' . self::buildPath() . '/' . self::manifest($path)['file'];
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

        $manifestPath = './' . self::buildPath() . '/manifest.json';

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

        // ! the hot file `npm run dev` does not work within tinymce iframe (only production mode supported `npm run build`). Need to find a solution for dev environment) * data of the field is not being saved when css with hot url injected

        if (!self::hotAsset()) {

            if (isset($config['editor_css'])) {
                $reqs = TinyMCEConfig::config()->get('editor_css');

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

    public static function buildPath()
    {
        $config = Config::inst()->get('Swordfox\Vite');

        if (isset($config['buildPath'])) {

          $buildPath = $config['buildPath'];

          // safely remove slash at the start if exists
          if (substr($buildPath, 0, 1) == '/') {
              $buildPath = substr($buildPath, 1);
          }

          // safely remove slash at the end if exists
          if (substr($buildPath, strlen($buildPath) - 1) == '/') {
              $buildPath = substr($buildPath, 0, strlen($buildPath) - 1);
          }
        } else {
          $buildPath = 'build';
        }

        return $buildPath;
    }

    private function getBase()
    {
        return Environment::getEnv('APP_URL_CDN') ?? Environment::getEnv('SS_BASE_URL');
    }

    private function writeHTML($html)
    {
        return DBField::create_field('HTMLFragment', $html);
    }
}
