<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Entry;
use AppBundle\Form\EntryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Entry controller.
 *
 * @Route("admin/entry")
 */
class EntryController extends Controller
{
    /**
     * List all entries.
     *
     * @Route("/", name="admin_entry_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findAll();

        return $this->render('admin/entry/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    /**
     * Save a new entry.
     *
     * @Route("/new", name="admin_entry_new")
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $url = $this->generateUrl('admin_entry_edit', ['id' => $entry->getId()]);

            $translated = $translator->trans('success.new');
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
    public function editAction(Request $request, TranslatorInterface $translator, Entry $entry)
    {
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $translated = $translator->trans('success.edit');
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
    public function deleteAction(TranslatorInterface $translator, Entry $entry)
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%entry%', $entry->getName(), $translator->trans('success.deleted.entry'));

        $em->remove($entry);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
