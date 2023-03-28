<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Post;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class PostController extends AbstractController
{
    /**
     * Gather all information for the post detail page.
     *
     * @Route("/post/{slug}", name="post_show")
     * @Entity("post", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $prev = $postRepository->findByTimestamp($post);
        $next = $postRepository->findByTimestamp($post, '>', 'ASC');

        $tpl = 'frontend/post/show.html.twig';
        if ($request->isXmlHttpRequest()) {
            $tpl = 'frontend/post/ajax-show.html.twig';
        }

        return $this->render($tpl, [
            'post' => $post,
            'prev' => $prev,
            'next' => $next,
        ]);
    }

    /**
     * Route for paginate posts.
     *
     * @Route("/ajax/posts/{page}", name="post_pagiante_ajax", requirements={"page": "\d+"}, condition="request.isXmlHttpRequest()")
     */
    public function ajaxPaginate(PostRepository $postRepository, int $page = 1): Response
    {
        $query = $postRepository->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $posts = PaginationHelper::paginate($query, Post::PAGINATION_QUANTITY, $page);

        return $this->render('frontend/post/ajax-list.html.twig', [
            'posts' => $posts,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
