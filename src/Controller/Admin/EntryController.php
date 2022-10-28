<?php

namespace App\Controller\Admin;

use App\Entity\Entry;
use App\Form\EntryType;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Entry controller.
 *
 * @Route("admin/entry")
 */
class EntryController extends AbstractController
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
     * List all entries.
     *
     * @Route("/", name="admin_entry_index", methods={"GET"})
     */
    public function index(EntryRepository $entryRepository): Response
    {
        return $this->render('admin/entry/index.html.twig', [
            'entries' => $entryRepository->findAll(),
        ]);
    }

    /**
     * Save a new entry.
     *
     * @Route("/new", name="admin_entry_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            $url = $this->generateUrl('admin_entry_edit', ['id' => $entry->getId()]);

            $translated = $translator->trans('flash.success.new', [
                '%link%' => $url,
                '%name%' => $entry->getName(),
            ]);

            $this->addFlash('success', $translated);

            return $this->redirectToRoute('admin_entry_new');
        }

        return $this->render('admin/entry/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing entry.
     *
     * @Route("/edit/{id}", name="admin_entry_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Entry $entry): Response
    {
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_entry_edit', ['id' => $entry->getId()]);
        }

        return $this->render('admin/entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($entry)->createView(),
        ]);
    }

    /**
     * Delete a entry.
     *
     * @Route("/delete/{id}", name="admin_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, Entry $entry): RedirectResponse
    {
        $form = $this->createDeleteForm($entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            $translated = $translator->trans('flash.success.deleted.entry', [
                '%entry%' => $entry->getName(),
            ]);

            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Entry $entry): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_entry_delete', ['id' => $entry->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
