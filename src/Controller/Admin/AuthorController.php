<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Author controller.
 *
 * @Route("admin/author")
 */
class AuthorController extends AbstractController
{
    /**
     * List all authors.
     *
     * @Route("/", name="admin_authors_index")
     */
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('admin/author/index.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    /**
     * Save an new author.
     *
     * @Route("/new", name="admin_author_new")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();

            $url = $this->generateUrl('admin_author_edit', ['id' => $author->getId()]);

            $translated = $translator->trans('flash.success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$author->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_author_new');
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save an existing author.
     *
     * @Route("/edit/{id}", name="admin_author_edit")
     */
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();

            $this->addFlash('success', 'flash.success.edit');
        }

        return $this->render('admin/author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($author)->createView(),
        ]);
    }

    /**
     * Delete an author.
     *
     * @Route("/delete/{id}", name="admin_author_delete")
     */
    public function delete(Request $request, TranslatorInterface $translator, Author $author): RedirectResponse
    {
        $form = $this->createDeleteForm($author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($author);
            $em->flush();

            $translated = str_replace('%author%', $author->getName(), $translator->trans('flash.success.deleted.author'));
            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Author $author): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_author_delete', ['id' => $author->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
