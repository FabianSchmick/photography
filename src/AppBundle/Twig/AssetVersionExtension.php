<?php

namespace AppBundle\Twig;

class AssetVersionExtension extends \Twig_Extension
{
    /**
     * @var string  $appDir     Application directory
     */
    private $appDir;

    /**
     * @var string $assetsDir   Assets directory e.g. /bundles/app/
     */
    private $assetsDir;

    /**
     * @var array   $paths      Path of the asset files
     */
    private $paths = array();

    /**
     * AssetVersionExtension constructor.
     *
     * @param string    $appDir
     * @param string    $assetsDir
     */
    public function __construct($appDir, $assetsDir)
    {
        $this->appDir = $appDir;
        $this->assetsDir = $assetsDir;
    }

    /**
     * Register Twig function
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('asset_version', array($this, 'getAssetVersion')),
        );
    }

    /**
     * Get the current asset
     *
     * @param   string        $filename       The requested filename to find
     * @return  string                        The real filename
     * @throws  \Exception
     */
    public function getAssetVersion($filename)
    {
        if (count($this->paths) === 0) {
            $manifestPath = $this->appDir . '/rev-manifest.json';

            if (!file_exists($manifestPath)) {
                throw new \Exception(sprintf('Cannot find manifest file: "%s"', $manifestPath));
            }

            $this->paths = json_decode(file_get_contents($manifestPath), true);
        }

        if (!isset($this->paths[$filename])) {
            throw new \Exception(sprintf('Cannot find manifest file: "%s"', $manifestPath));
        }

        return $this->assetsDir . $this->paths[$filename];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'asset_version';
    }
}
