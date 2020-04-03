<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Entry;
use App\Repository\EntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class EntryController extends AbstractController
{
    /**
     * Gather all information for the entry detail page.
     *
     * @Route("/entry/{slug}", name="entry_show")
     * @Entity("entry", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(Entry $entry, EntryRepository $entryRepository): Response
    {
        $prev = $entryRepository->findByTimestamp($entry);
        $next = $entryRepository->findByTimestamp($entry, '>', 'ASC');

        return $this->render('frontend/entry/show.html.twig', [
            'entry' => $entry,
            'prev' => $prev,
            'next' => $next,
        ]);
    }

    /**
     * Gather all information for the ajax entry detail page (lightbox).
     *
     * @Route("/ajax/entry/{id}/", name="entry_show_ajax", requirements={"id": "\d+"}, condition="request.isXmlHttpRequest()")
     */
    public function ajaxShow(Entry $entry): Response
    {
        return $this->render('frontend/entry/ajax-show.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Route for paginate entries.
     *
     * @Route("/ajax/entries/{page}", name="entry_pagiante_ajax", requirements={"page": "\d+"}, condition="request.isXmlHttpRequest()")
     */
    public function ajaxPaginate(EntryRepository $entryRepository, $page = 1): Response
    {
        $query = $entryRepository->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, Entry::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
