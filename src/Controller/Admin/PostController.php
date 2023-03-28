<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Post controller.
 *
 * @Route("admin/post")
 */
class PostController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * List all posts.
     *
     * @Route("/", name="admin_post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('admin/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * Save a new post.
     *
     * @Route("/new", name="admin_post_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $url = $this->generateUrl('admin_post_edit', ['id' => $post->getId()]);

            $translated = $translator->trans('flash.success.new', [
                '%link%' => $url,
                '%name%' => $post->getName(),
            ]);

            $this->addFlash('success', $translated);

            return $this->redirectToRoute('admin_post_new');
        }

        return $this->render('admin/post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing post.
     *
     * @Route("/edit/{id}", name="admin_post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($post)->createView(),
        ]);
    }

    /**
     * Delete a post.
     *
     * @Route("/delete/{id}", name="admin_post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, Post $post): RedirectResponse
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();

            $translated = $translator->trans('flash.success.deleted.post', [
                '%post%' => $post->getName(),
            ]);

            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Post $post): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_post_delete', ['id' => $post->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
