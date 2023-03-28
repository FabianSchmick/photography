<?php

namespace App\EventListener;

use App\Entity\Post;
use Gedmo\Sluggable\SluggableListener as GedmoSluggableListener;
use Gedmo\Sluggable\Util\Urlizer;

class SluggableListener extends GedmoSluggableListener
{
    public function __construct()
    {
        $this->setTransliterator([self::class, 'transliterate']);
    }

    /**
     * Add the unique ID for Posts as a prefix.
     */
    public static function transliterate(string $text, string $separator, $objectBeingSlugged): string
    {
        if ($objectBeingSlugged instanceof Post) {
            $text = $text.$separator.substr($objectBeingSlugged->getId(), 0, 4);
        }

        $text = Urlizer::unaccent($text);
        $text = Urlizer::transliterate($text, $separator);

        return $text;
    }
}
