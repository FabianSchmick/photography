<?php

namespace AppBundle\Service;

use HTMLPurifier;
use HTMLPurifier_Config;

class CoreService
{
    /**
     * @param string $string The string to purify
     *
     * @return string $string     The purified string
     */
    public function purifyString($string)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', ['a', 'b', 'strong', 'ul', 'li', 'p', 'br']);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($string);
    }
}
