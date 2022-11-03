<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * https://github.com/twigphp/Twig/issues/3681#issuecomment-1159029881.
 */
class EnumExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum', [$this, 'enum']),
        ];
    }

    public function enum(string $fullClassName): object
    {
        $parts = explode('::', $fullClassName);
        $className = $parts[0];
        $constant = $parts[1] ?? null;

        if (!enum_exists($className)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not an enum.', $className));
        }

        if ($constant) {
            return constant($fullClassName);
        }

        return new class($fullClassName) {
            public function __construct(private string $fullClassName)
            {
            }

            public function __call(string $caseName, array $arguments): mixed
            {
                return call_user_func_array([$this->fullClassName, $caseName], $arguments);
            }
        };
    }
}
