<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TagController extends Controller
{
    /**
     * Filter entries by a tag.
     *
     * @Route("/tag/{slug}", name="tag_show")
     * @ParamConverter("tag", class="AppBundle:Tag", options={"repository_method" = "findOneByCriteria"})
     */
    public function showAction(Request $request, Tag $tag)
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
    public function paginateAction(Request $request, Tag $tag, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->findEntriesByTag($tag);
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, Entry::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
