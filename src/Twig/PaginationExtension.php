<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class PaginationExtension extends AbstractExtension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * PaginationExtension constructor.
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator, Environment $engine)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->environment = $engine;
    }

    /**
     * Register Twig function.
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', [$this, 'getPagination']),
        ];
    }

    /**
     * Generates a pagination and adds breaks points with prev and next btn.
     *
     * @param int    $current Current page
     * @param int    $last    Last page
     * @param string $path    Name of the controller which paginates
     * @param array  $options Additional options like a query for the paginate controller or css classes name
     */
    public function getPagination(int $current, int $last, string $path, array $options = []): Markup
    {
        $paginations = $parameters = [];
        $start = 1;
        $end = $last;
        $options['active'] = $options['class'] ?? 'active';
        $options['prev'] = $options['prev'] ?? 'prev';
        $options['next'] = $options['next'] ?? 'next';

        // Add parameters for the route
        if (isset($options['q'])) {
            if (is_array($options['q'])) {
                $parameters = $options['q'];
            } elseif ($options['q'] != '') {
                $parameters['q'] = $options['q'];
            }
        }

        // Break the sites down to max 2 prev and 2 next visible
        if ($current - 2 > 1) {
            $start = $current - 2;
        }

        if ($current + 2 < $last) {
            $end = $current + 2;
        }

        // Add prev and next btn
        if ($current > 1) {
            $parameters['page'] = $current - 1;
            $paginations[0] = [
                'path' => $this->router->generate($path, $parameters),
                'class' => $options['prev'],
                'text' => $this->translator->trans('pagination.prev'),
            ];
        }

        if ($current < $last) {
            $parameters['page'] = $current + 1;
            $paginations[$last + 1] = [
                'path' => $this->router->generate($path, $parameters),
                'class' => $options['next'],
                'text' => $this->translator->trans('pagination.next'),
            ];
        }

        // Add pages with their numbers
        for ($i = $start; $i <= $end; ++$i) {
            $class = ($i == $current ? $options['active'] : '');
            $parameters['page'] = $i;
            $paginations[$i] = [
                'path' => $this->router->generate($path, $parameters),
                'class' => $class,
                'text' => $i,
            ];
        }

        ksort($paginations);

        $html = $this->environment->render('templates/pagination.html.twig', [
            'paginations' => $paginations,
        ]);

        return new Markup($html, 'UTF-8');
    }

    public function getName(): string
    {
        return 'paginate';
    }
}
