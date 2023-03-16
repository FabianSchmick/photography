<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;

/**
 * Filter which converts svg file and returns an inline svg string
 *
 * Inspired by https://github.com/manuelodelain/svg-twig-extension
 */
class InlineSvgExtension extends AbstractExtension
{
    public function __construct(private readonly string $publicDir)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('inline_svg', [$this, 'getInlineSvg']),
        ];
    }

    /**
     * Get an inline svg.
     *
     * @param string $filename Path to the svg file
     *
     * @throws \Exception
     */
    public function getInlineSvg(string $filename): Markup
    {
        $fullPath = $this->publicDir.$filename;

        if (!file_exists($fullPath)) {
            throw new \Exception(sprintf('Cannot find svg file: "%s"', $fullPath));
        }

        $svgString = file_get_contents($fullPath);

        // remove annoying xml version added by as XML method
        $svgString = preg_replace("#<\?xml version.*?>#", '', $svgString);

        // remove comments
        $svgString = preg_replace('#<!--.*-->#', '', $svgString);

        return new Markup($svgString, 'UTF-8');
    }
}
