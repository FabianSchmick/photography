<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
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
     * Gather all information for the entry detail page.
     *
     * @Route("/entry/{slug}", name="entry_show")
     * @ParamConverter("entry", class="AppBundle:Entry", options={"repository_method" = "findOneByCriteria"})
     */
    public function entryShowAction(Request $request, Entry $entry)
    {
        return $this->render('frontend/entry/show.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Gather all information for the ajax entry detail page (lightbox).
     *
     * @Route("/ajax/entry/{id}/", name="ajax_entry_show", requirements={"id" = "\d+"}, condition="request.isXmlHttpRequest()")
     */
    public function ajaxEntryShowAction(Request $request, Entry $entry)
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
    public function paginateEntriesAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, 10, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * Filter entries by a tag.
     *
     * @Route("/tag/{slug}", name="tag_show")
     * @ParamConverter("tag", class="AppBundle:Tag", options={"repository_method" = "findOneByCriteria"})
     */
    public function tagShowAction(Request $request, Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $relatedTags = $em->getRepository('AppBundle:Tag')->findRelatedTagsByTag($tag);

        return $this->render('frontend/tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $relatedTags,
        ]);
    }

    /**
     * Route for paginate by tag.
     *
     * @Route("/ajax/tag/{slug}/{page}", name="paginate_by_tag", requirements={"page": "\d+"}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("tag", class="AppBundle:Tag", options={"repository_method" = "findOneByCriteria", "mapping": {"slug": "slug"}})
     */
    public function paginateByTagAction(Request $request, Tag $tag, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->findEntriesByTag($tag);
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, 10, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
