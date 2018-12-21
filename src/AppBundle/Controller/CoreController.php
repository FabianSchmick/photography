<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class CoreController extends Controller
{
    /**
     * @Route("/", name="locale", defaults={"redirect": "homepage"})
     * @Route("/admin/language/{_locale}/",
     *     defaults={"redirect": "admin_index"},
     *     requirements={"_locale": "%app.locales%"},
     *     name="localeAdmin"
     * )
     */
    public function localeAction(Request $request, $redirect)
    {
        $this->get('session')->set('_locale', $this->container->getParameter('locale'));

        if ($requestedLocale = $request->attributes->get('_locale')) {
            $this->get('session')->set('_locale', $requestedLocale);
        }

        return $this->redirectToRoute($redirect);
    }

    /**
     * @Route("/sitemap.{_format}", name="sitemap", requirements={"_format" = "xml"})
     */
    public function sitemapAction(Request $request, RouterInterface $router)
    {
        $em = $this->getDoctrine()->getManager();
        $locales = explode('|', $this->getParameter('app.locales'));

        $hostname = $request->getScheme().'://'.$request->getHost();

        $urls = [];
        foreach ($locales as $locale) {
            $urls[] = [
                'loc' => $router->generate('homepage', ['_locale' => $locale]),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ];

            $entries = $em->getRepository('AppBundle:Entry')->findAll();

            foreach ($entries as $entry) {
                $entry->setTranslatableLocale($locale);
                $em->refresh($entry);

                $urls[] = [
                    'loc' => $router->generate('entry_show', ['_locale' => $locale, 'slug' => $entry->getSlug()]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            $tags = $em->getRepository('AppBundle:Tag')->findAll();

            foreach ($tags as $tag) {
                $tag->setTranslatableLocale($locale);
                $em->refresh($tag);

                $urls[] = [
                    'loc' => $router->generate('tag_show', ['_locale' => $locale, 'slug' => $tag->getSlug()]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }
        }

        return $this->render('sitemap.xml.twig', [
            'urls' => $urls,
            'hostname' => $hostname,
        ]);
    }
}
