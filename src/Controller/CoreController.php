<?php

namespace App\Controller;

use App\Doctrine\PaginationHelper;
use App\Entity\Entry;
use App\Entity\File;
use App\Entity\Tag;
use App\Entity\Tour;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CoreController extends AbstractController
{
    /**
     * @Route("/", name="core_switch_locale_frontend", defaults={"redirect": "homepage"})
     * @Route("/admin/language/{_locale}/", defaults={"redirect": "admin_index"},
     *     requirements={"_locale": "%app.locales%"}, name="core_switch_locale_admin")
     */
    public function locale(Request $request, SessionInterface $session, $redirect): RedirectResponse
    {
        $session->set('_locale', $this->getParameter('locale'));

        if ($requestedLocale = $request->attributes->get('_locale')) {
            $session->set('_locale', $requestedLocale);
        }

        return $this->redirectToRoute($redirect);
    }

    /**
     * @Route("/download/{file}", name="core_download_file")
     * @ParamConverter("file", class="App\Entity\File", options={"mapping": {"file": "fileName"}})
     */
    public function downloadFile(File $file, UploaderHelper $uploaderHelper): BinaryFileResponse
    {
        $projectRoot = $this->getParameter('kernel.project_dir');
        $filePath = $projectRoot.'/public'.$uploaderHelper->asset($file, 'file');

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getOriginalName());

        return $response;
    }

    /**
     * @Route("/sitemap.{_format}", name="core_sitemap", requirements={"_format": "xml"})
     */
    public function sitemap(Request $request, RouterInterface $router): Response
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

            $query = $em->getRepository(Tour::class)->getFindAllQuery();
            $pages = PaginationHelper::getPagesCount($query, Tour::PAGINATION_QUANTITY);

            for ($i = 1; $i < $pages + 1; ++$i) {
                $urls[] = [
                    'loc' => $router->generate('tour_index', ['_locale' => $locale, 'page' => $i]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            $urls[] = [
                'loc' => $router->generate('tour_map', ['_locale' => $locale]),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];

            $entries = $em->getRepository(Entry::class)->findAll();

            foreach ($entries as $entry) {
                $entry->setTranslatableLocale($locale);
                $em->refresh($entry);

                $urls[] = [
                    'loc' => $router->generate('entry_show', ['_locale' => $locale, 'slug' => $entry->getSlug()]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            $tags = $em->getRepository(Tag::class)->findAll();

            foreach ($tags as $tag) {
                $tag->setTranslatableLocale($locale);
                $em->refresh($tag);

                $urls[] = [
                    'loc' => $router->generate('tag_show', ['_locale' => $locale, 'slug' => $tag->getSlug()]),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            $tours = $em->getRepository(Tour::class)->findAll();

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
