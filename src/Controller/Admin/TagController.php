<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Tag controller.
 *
 * @Route("admin/tag")
 */
class TagController extends AbstractController
{
    /**
     * List all tags.
     *
     * @Route("/", name="admin_tag_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('App:Tag')->findAll();

        return $this->render('admin/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * Save a new tag.
     *
     * @Route("/new", name="admin_tag_new")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();

            $url = $this->generateUrl('admin_tag_edit', ['id' => $tag->getId()]);

            $translated = $translator->trans('success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$tag->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_tag_new');
        }

        return $this->render('admin/tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing tag.
     *
     * @Route("/edit/{id}", name="admin_tag_edit")
     */
    public function edit(Request $request, TranslatorInterface $translator, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();

            $translated = $translator->trans('success.edit');
            $this->addFlash(
                'success',
                $translated.'.'
            );
        }

        return $this->render('admin/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a tag.
     *
     * @Route("/delete/{id}", name="admin_tag_delete")
     */
    public function delete(TranslatorInterface $translator, Tag $tag): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%tag%', $tag->getName(), $translator->trans('success.deleted.tag'));

        $em->remove($tag);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}