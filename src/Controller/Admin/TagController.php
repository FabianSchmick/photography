<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * List all tags.
     *
     * @Route("/", name="admin_tag_index", methods={"GET"})
     */
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('admin/tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    /**
     * Save a new tag.
     *
     * @Route("/new", name="admin_tag_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $url = $this->generateUrl('admin_tag_edit', ['id' => $tag->getId()]);

            $translated = $translator->trans('flash.success.new', [
                '%link%' => $url,
                '%name%' => $tag->getName(),
            ]);

            $this->addFlash('success', $translated);

            return $this->redirectToRoute('admin_tag_new');
        }

        return $this->render('admin/tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing tag.
     *
     * @Route("/edit/{id}", name="admin_tag_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_tag_edit', ['id' => $tag->getId()]);
        }

        return $this->render('admin/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($tag)->createView(),
        ]);
    }

    /**
     * Delete a tag.
     *
     * @Route("/delete/{id}", name="admin_tag_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, Tag $tag): RedirectResponse
    {
        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($tag);
            $this->entityManager->flush();

            $translated = $translator->trans('flash.success.deleted.tag', [
                '%tag%' => $tag->getName(),
            ]);

            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Tag $tag): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_tag_delete', ['id' => $tag->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
