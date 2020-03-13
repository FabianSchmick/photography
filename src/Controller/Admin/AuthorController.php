<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Author controller.
 *
 * @Route("admin/author")
 */
class AuthorController extends Controller
{
    /**
     * List all authors.
     *
     * @Route("/", name="admin_authors_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('App:Author')->findAll();

        return $this->render('admin/author/index.html.twig', [
            'authors' => $authors,
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

            $translated = $translator->trans('success.new');
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
    public function edit(Request $request, TranslatorInterface $translator, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();

            $translated = $translator->trans('success.edit');
            $this->addFlash(
                'success',
                $translated.'.'
            );
        }

        return $this->render('admin/author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete an author.
     *
     * @Route("/delete/{id}", name="admin_author_delete")
     */
    public function delete(TranslatorInterface $translator, Author $author): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%author%', $author->getName(), $translator->trans('success.deleted.author'));

        $em->remove($author);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
