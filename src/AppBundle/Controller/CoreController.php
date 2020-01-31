<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\File;
use AppBundle\Entity\Tour;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
    public function localeAction(Request $request, $redirect): RedirectResponse
    {
        $this->get('session')->set('_locale', $this->container->getParameter('locale'));

        if ($requestedLocale = $request->attributes->get('_locale')) {
            $this->get('session')->set('_locale', $requestedLocale);
        }

        return $this->redirectToRoute($redirect);
    }

    /**
     * @Route("/download/{file}", name="download_file")
     * @ParamConverter("file", class="AppBundle:File", options={"mapping": {"file": "fileName"}})
     */
    public function downloadFileAction(File $file, UploaderHelper $uploaderHelper): BinaryFileResponse
    {
        $projectRoot = $this->getParameter('kernel.project_dir');
        $filePath = $projectRoot.'/web'.$uploaderHelper->asset($file, 'file');

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getOriginalName());

        return $response;
    }

    /**
     * @Route("/sitemap.{_format}", name="sitemap", requirements={"_format" = "xml"})
     */
    public function sitemapAction(Request $request, RouterInterface $router): Response
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

            $query = $em->getRepository('AppBundle:Tour')->getFindAllQuery();
            $pages = PaginationHelper::getPagesCount($query, Tour::PAGINATION_QUANTITY);

            for ($i = 1; $i < $pages + 1; ++$i) {
                $urls[] = [
                    'loc' => $router->generate('tour_index_paginated', ['_locale' => $locale, 'page' => $i]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

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

            $tours = $em->getRepository('AppBundle:Tour')->findAll();

            foreach ($tours as $tour) {
                $tour->setTranslatableLocale($locale);
                $em->refresh($tour);

                $urls[] = [
                    'loc' => $router->generate('tour_show', ['_locale' => $locale, 'slug' => $tour->getSlug()]),
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
