<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Entry;
use App\Repository\EntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function show(Request $request, Entry $entry, EntryRepository $entryRepository): Response
    {
        $prev = $entryRepository->findByTimestamp($entry);
        $next = $entryRepository->findByTimestamp($entry, '>', 'ASC');

        $tpl = 'frontend/entry/show.html.twig';
        if ($request->isXmlHttpRequest()) {
            $tpl = 'frontend/entry/ajax-show.html.twig';
        }

        return $this->render($tpl, [
            'entry' => $entry,
            'prev' => $prev,
            'next' => $next,
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
