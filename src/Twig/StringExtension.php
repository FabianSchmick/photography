<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    /**
     * Register Twig filter.
     *
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('natural_language_join', [$this, 'naturalLanguageJoin']),
        ];
    }

    /**
     * Join a string with a natural language conjunction at the end.
     */
    public function naturalLanguageJoin(array $list): ?string
    {
        $last = array_pop($list);

        if ($list) {
            return implode(', ', $list).' & '.$last;
        }

        return $last;
    }

    public function getName(): string
    {
        return 'string';
    }
}
