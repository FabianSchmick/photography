<?php

namespace App\Service;

use App\Entity\Tour;
use HTMLPurifier;
use HTMLPurifier_Config;
use phpGPX\phpGPX;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CoreService
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * CoreService constructor.
     */
    public function __construct(UploaderHelper $uploaderHelper, $publicDir)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->publicDir = $publicDir;
    }

    /**
     * @param null|string $string The string to purify
     *
     * @return string $string     The purified string
     */
    public function purifyString(?string $string): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', ['a', 'b', 'strong', 'ul', 'li', 'p', 'br']);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($string);
    }

    /**
     * Sets the gpx stats data for a track.
     *
     * @param Tour $tour Tour entity
     */
    public function setGpxData(Tour &$tour): void
    {
        $gpx = new phpGPX();

        $file = $gpx->load($this->publicDir.$this->uploaderHelper->asset($tour->getFile(), 'file'));

        $tour->setGpxData($file->tracks[0]);
    }
}
