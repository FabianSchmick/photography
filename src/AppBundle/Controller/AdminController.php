<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Location;
use AppBundle\Entity\Tag;
use AppBundle\Service\AuthorService;
use AppBundle\Service\CoreService;
use AppBundle\Service\EntryService;
use AppBundle\Service\LocationService;
use AppBundle\Service\TagService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Admin controller.
 *
 * @Route("admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        // Till I don't know what to display here, redirect to new entry
        return $this->redirect($this->generateUrl('entry_new'));
//        return $this->render('admin/index.html.twig', []);
    }

    /**
     * List all entries
     *
     * @Route("/entry/", name="entry_index")
     */
    public function entryIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findAll();

        return $this->render('admin/entry/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    /**
     * Save an new entry
     *
     * @Route("/entry/new", name="entry_new")
     */
    public function entryNewAction(Request $request, TranslatorInterface $translator, EntryService $entryService)
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('AppBundle:Author')->findAll();
        $locations = $em->getRepository('AppBundle:Location')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        if ($newEntry = $request->request->get('new')) {
            $image = $request->files->get('new');

            $entry = $entryService->saveEntry($newEntry, $image);
            $url = $this->generateUrl('entry_edit', ['id' => $entry->getId()]);

            $translated = $translator->trans('success.new');
            $this->addFlash(
                'success',
                $translated . ': <a class="alert-link" href="' . $url . '">' . $entry->getTitle() . '</a>.'
            );
        }

        return $this->render('admin/entry/new.html.twig', [
            'authors'  => $authors,
            'locations' => $locations,
            'tags' => $tags,
        ]);
    }

    /**
     * Save an existing entry
     *
     * @Route("/entry/edit/{id}", name="entry_edit")
     */
    public function entryEditAction(Request $request, EntryService $entryService, TranslatorInterface $translator, Entry $entry)
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('AppBundle:Author')->findAll();
        $locations = $em->getRepository('AppBundle:Location')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        if ($editEntry = $request->request->get('edit')) {
            $image = $request->files->get('edit');

            $entryService->saveEntry($editEntry, $image);

            $translated = $translator->trans('success.edit');
            $this->addFlash(
                'success',
                $translated . '.'
            );
        }

        return $this->render('admin/entry/edit.html.twig', [
            'entry' => $entry,
            'authors'  => $authors,
            'locations' => $locations,
            'tags'  => $tags,
        ]);
    }

    /**
     * Delete an entry
     *
     * @Route("/entry/delete/{id}", name="entry_delete")
     */
    public function entryDeleteAction(Request $request, Entry $entry, CoreService $coreService)
    {
        $em = $this->getDoctrine()->getManager();

        $coreService->deleteImage($entry->getImage());

        $em->remove($entry);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_index'));
    }

    /**
     * Renders the admin navigation sidebar
     */
    public function renderSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findBy([], ['timestamp' => 'DESC']);
        $authors = $em->getRepository('AppBundle:Author')->findBy([], ['name' => 'ASC']);
        $locations = $em->getRepository('AppBundle:Location')->findBy([], ['name' => 'ASC']);
        $tags = $em->getRepository('AppBundle:Tag')->findBy([], ['name' => 'ASC']);

        return $this->render('admin/inc/sidebar.html.twig', [
            'entries' => $entries,
            'authors'  => $authors,
            'locations' => $locations,
            'tags' => $tags,
        ]);
    }
}
