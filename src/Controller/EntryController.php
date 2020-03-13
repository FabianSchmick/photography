<?php

namespace App\Controller;

use App\Doctrine\PaginationHelper;
use App\Entity\Entry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class EntryController extends Controller
{
    /**
     * Gather all information for the entry detail page.
     *
     * @Route("/entry/{slug}", name="entry_show")
     * @ParamConverter("entry", class="App:Entry", options={"repository_method" = "findOneByCriteria"})
     */
    public function show(Entry $entry): Response
    {
        $em = $this->getDoctrine()->getManager();

        $prev = $em->getRepository('App:Entry')->findByTimestamp($entry);
        $next = $em->getRepository('App:Entry')->findByTimestamp($entry, '>', 'ASC');

        return $this->render('frontend/entry/show.html.twig', [
            'entry' => $entry,
            'prev' => $prev,
            'next' => $next,
        ]);
    }

    /**
     * Gather all information for the ajax entry detail page (lightbox).
     *
     * @Route("/ajax/entry/{id}/", name="ajax_entry_show", requirements={"id" = "\d+"}, condition="request.isXmlHttpRequest()")
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
     * @Route("/ajax/entries/{page}", name="paginate_entries", requirements={"page": "\d+"}, condition="request.isXmlHttpRequest()")
     */
    public function paginate($page = 1): Response
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('App:Entry')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, Entry::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
