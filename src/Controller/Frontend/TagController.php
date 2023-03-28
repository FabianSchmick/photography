<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
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
     * Filter posts by a tag.
     *
     * @Route("/tag/{slug}", name="tag_show")
     * @Entity("tag", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(Tag $tag, TagRepository $tagRepository): Response
    {
        $relatedTags = $tagRepository->findRelatedTagsByTag($tag);

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
    public function ajaxPaginate(Tag $tag, PostRepository $postRepository, int $page = 1): Response
    {
        $query = $postRepository->findPostsByTagQuery($tag);
        $pages = PaginationHelper::getPagesCount($query);
        $posts = PaginationHelper::paginate($query, Post::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/post/ajax-list.html.twig', [
            'posts' => $posts,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
