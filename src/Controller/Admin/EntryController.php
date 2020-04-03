<?php

namespace App\Controller\Admin;

use App\Entity\Entry;
use App\Form\EntryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * List all entries.
     *
     * @Route("/", name="admin_entry_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('App:Entry')->findAll();

        return $this->render('admin/entry/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    /**
     * Save a new entry.
     *
     * @Route("/new", name="admin_entry_new")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $url = $this->generateUrl('admin_entry_edit', ['id' => $entry->getId()]);

            $translated = $translator->trans('flash.success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$entry->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_entry_new');
        }

        return $this->render('admin/entry/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing entry.
     *
     * @Route("/edit/{id}", name="admin_entry_edit")
     */
    public function edit(Request $request, TranslatorInterface $translator, Entry $entry): Response
    {
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $translated = $translator->trans('flash.success.edit');
            $this->addFlash(
                'success',
                $translated.'.'
            );
        }

        return $this->render('admin/entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a entry.
     *
     * @Route("/delete/{id}", name="admin_entry_delete")
     */
    public function delete(TranslatorInterface $translator, Entry $entry): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%entry%', $entry->getName(), $translator->trans('flash.success.deleted.entry'));

        $em->remove($entry);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
