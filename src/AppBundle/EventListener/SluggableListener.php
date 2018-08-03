<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Entry;
use Gedmo\Sluggable\SluggableListener as GedmoSluggableListener;
use Gedmo\Sluggable\Util\Urlizer;

class SluggableListener extends GedmoSluggableListener
{
    public function __construct()
    {
        $this->setTransliterator(['\AppBundle\EventListener\SluggableListener', 'transliterate']);
    }

    /**
     * Since transliterate will convert "ä" to an "a", I added this hack to call
     * unaccent first so it is converted to "ae" first.
     *
     * And for hash only 4 letters.
     *
     * @param string $text
     * @param string $separator
     *
     * @return string $text
     */
    public static function transliterate($text, $separator = '-', $objectBeingSlugged)
    {
        if ($objectBeingSlugged instanceof Entry) {
            $text = $text.'-'.substr($objectBeingSlugged->getId(), 0, 4);
        }

        $text = Urlizer::unaccent($text);
        $text = Urlizer::transliterate($text, $separator);

        return $text;
    }
}
