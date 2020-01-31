<?php

namespace AppBundle\Twig;

use Exception;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;

/**
 * Class InlineSvgExtension.
 *
 * Filter which converts svg file and returns an inline svg string
 *
 * Usage:
 * Add or replace attributes with the attr property:
 * {{ asset('assets/img/logo.svg')|inline_svg({attr: {class: 'inline-svg', id: 'logo'}}) }}
 *
 * Add CSS classes:
 * {{ asset('assets/img/logo.svg')|inline_svg({classes: 'add-classname another-classname'}) }}
 *
 * Inspired by https://github.com/manuelodelain/svg-twig-extension
 */
class InlineSvgExtension extends AbstractExtension
{
    /**
     * Project web directory.
     *
     * @var string
     */
    private $webDir;

    /**
     * InlineSvgExtension constructor.
     *
     * @param string $webDir
     */
    public function __construct(string $webDir)
    {
        $this->webDir = $webDir;
    }

    /**
     * Register Twig function.
     *
     * @return \Twig\TwigFilter[]
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
     * @param array  $params   Optional parameters
     *
     * @return Markup
     *
     * @throws \Exception
     */
    public function getInlineSvg(string $filename, array $params = []): Markup
    {
        $fullPath = $this->webDir.$filename;

        if (!file_exists($fullPath)) {
            throw new Exception(sprintf('Cannot find svg file: "%s"', $fullPath));
        }

        $svgString = file_get_contents($fullPath);

        $hasAttr = array_key_exists('attr', $params);
        $hasClasses = array_key_exists('classes', $params);

        if ($hasAttr || $hasClasses) {
            $svg = simplexml_load_string($svgString);
            $attrs = $svg->attributes();
        }

        if ($hasAttr) {
            foreach ($params['attr'] as $key => $value) {
                if ($attrs[$key]) {
                    $attrs[$key] = $value;
                } else {
                    $svg->addAttribute($key, $value);
                }
            }
            $svgString = $svg->asXML();
        }

        if ($hasClasses) {
            if ($attrs['class']) {
                $attrs['class'] .= ' '.$params['classes'];
            } else {
                $svg->addAttribute('class', $params['classes']);
            }
            $svgString = $svg->asXML();
        }

        // remove annoying xml version added by as XML method
        $svgString = preg_replace("#<\?xml version.*?>#", '', $svgString);

        // remove comments
        $svgString = preg_replace('#<!--.*-->#', '', $svgString);

        return new Markup($svgString, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'inline_svg';
    }
}
