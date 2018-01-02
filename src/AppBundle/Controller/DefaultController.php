<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('frontend/index.html.twig', [
        ]);
    }

    /**
     * Gather all information for the entry detail page
     *
     * @Route("/entry/{slug}", name="entry_detail")
     * @ParamConverter("entry", class="AppBundle:Entry", options={"repository_method" = "findOneByCriteria"})
     */
    public function entryDetailAction(Request $request, Entry $entry)
    {
        return $this->render('frontend/entry.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Gather all information for the ajax entry detail page (lightbox)
     *
     * @Route("/ajax/entry/{id}/", name="ajax_entry_detail")
     */
    public function ajaxEntryDetailAction(Request $request, Entry $entry)
    {
        return $this->render('frontend/inc/ajaxEntry.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Route for paginate entries
     *
     * @Route("/entries/{page}", name="paginate_entries", requirements={"page": "\d+"})
     */
    public function paginateEntriesAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, 10, $page);

        return $this->render('frontend/inc/entries.html.twig', [
            'entries'   => $entries,
            'page'      => $page,
            'pages'     => $pages
        ]);
    }

    /**
     * Filter entries by tag
     *
     * @Route("/tag/{slug}", name="tag_filter")
     * @ParamConverter("tag", class="AppBundle:Tag", options={"repository_method" = "findOneByCriteria"})
     */
    public function tagFilterAction(Request $request, Tag $tag)
    {
        return $this->render('frontend/tag.html.twig', [
            'tag' => $tag
        ]);
    }

    /**
     * Route for paginate by tag
     *
     * @Route("/tag/{slug}/{page}", name="paginate_by_tag", requirements={"page": "\d+"})
     * @ParamConverter("tag", class="AppBundle:Tag", options={"repository_method" = "findOneByCriteria", "mapping": {"slug": "slug"}})
     */
    public function paginateByTagAction(Request $request, Tag $tag, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->findEntriesByTag($tag);
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, 10, $page);

        return $this->render('frontend/inc/entries.html.twig', [
            'entries'   => $entries,
            'page'      => $page,
            'pages'     => $pages
        ]);
    }

    /**
     * Change language
     *
     * @Route("admin/language/{locale}", name="changeLanguageAdmin")
     * @Route("/language/{locale}", name="changeLanguage")
     */
    public function changeLanguageAction($locale)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $routeName = $request->get('_route');
        $request->attributes->set('_locale', null);
        $this->get('session')->set('_locale', $locale);

        $redirect = 'homepage';
        if ($routeName == 'changeLanguageAdmin') {
            $redirect = 'admin_index';
        }

        return $this->redirect($this->generateUrl($redirect));
    }
}
