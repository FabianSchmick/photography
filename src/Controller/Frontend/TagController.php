<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Entry;
use App\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TagController extends AbstractController
{
    /**
     * Filter entries by a tag.
     *
     * @Route("/tag/{slug}", name="tag_show")
     * @Entity("tag", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(Tag $tag): Response
    {
        $em = $this->getDoctrine()->getManager();

        $relatedTags = $em->getRepository('App:Tag')->findRelatedTagsByTag($tag);

        return $this->render('frontend/tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $relatedTags,
        ]);
    }

    /**
     * Route for paginate by tag.
     *
     * @Route("/ajax/tag/{slug}/{page}", name="tag_pagiante_ajax", requirements={"page": "\d+"}, condition="request.isXmlHttpRequest()")
     * @Entity("tag", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function ajaxPaginate(Tag $tag, $page = 1): Response
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('App:Entry')->findEntriesByTag($tag);
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, Entry::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/entry/ajax-list.html.twig', [
            'entries' => $entries,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
