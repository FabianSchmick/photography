<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Location;
use AppBundle\Entity\Tag;
use AppBundle\Service\EntryService;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;


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
    public function entryNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('AppBundle:Author')->findAll();
        $locations = $em->getRepository('AppBundle:Location')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        if ($newEntry = $request->request->get('new')) {
            $image = $request->files->get('new');

            $entryService = $this->get('AppBundle\Service\EntryService');

            $entry = $entryService->saveEntry($newEntry, $image);
            $url = $this->generateUrl('entry_edit', ['id' => $entry->getId()]);

            $translated = $this->get('translator')->trans('success.new');
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
    public function entryEditAction(Request $request, Entry $entry)
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('AppBundle:Author')->findAll();
        $locations = $em->getRepository('AppBundle:Location')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        if ($editEntry = $request->request->get('edit')) {
            $image = $request->files->get('edit');

            $entryService = $this->get('AppBundle\Service\EntryService');

            $entryService->saveEntry($editEntry, $image);

            $translated = $this->get('translator')->trans('success.edit');
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
    public function entryDeleteAction(Request $request, Entry $entry)
    {
        $em = $this->getDoctrine()->getManager();

        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('image_directory') . '/' . $entry->getImage());
        $filesystem->remove($this->getParameter('image_directory') . '/thumb/' . $entry->getImage());

        $em->remove($entry);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_index'));
    }

    /**
     * List all authors
     *
     * @Route("/author/", name="author_index")
     */
    public function authorIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('AppBundle:Author')->findAll();

        return $this->render('admin/author/index.html.twig', [
            'author' => $author,
        ]);
    }

    /**
     * Save an new author
     *
     * @Route("/author/new", name="author_new")
     */
    public function authorNewAction(Request $request)
    {
        if ($newAuthor = $request->request->get('new')) {
            $authorService = $this->get('AppBundle\Service\AuthorService');

            $authorService->saveAuthor($newAuthor);
        }

        return $this->render('admin/author/new.html.twig', []);
    }

    /**
     * Save an existing author
     *
     * @Route("/author/edit/{id}", name="author_edit")
     */
    public function authorEditAction(Request $request, Author $author)
    {
        if ($editAuthor = $request->request->get('edit')) {
            $authorService = $this->get('AppBundle\Service\AuthorService');

            $authorService->saveAuthor($editAuthor);

            $translated = $this->get('translator')->trans('success.edit');
            $this->addFlash(
                'success',
                $translated . '.'
            );
        }

        return $this->render('admin/author/edit.html.twig', [
            'author'  => $author,
        ]);
    }

    /**
     * Delete an author
     *
     * @Route("/author/delete/{id}", name="author_delete")
     */
    public function authorDeleteAction(Request $request, Author $author)
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findEntriesByAuthor($author);

        foreach ($entries as $entry) {
            $entry->setAuthor(null);
            $em->persist($entry);
        }

        $em->remove($author);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_index'));
    }

    /**
     * List all locations
     *
     * @Route("/location/", name="location_index")
     */
    public function locationIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $location = $em->getRepository('AppBundle:Location')->findAll();

        return $this->render('admin/location/index.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * Save a new location
     *
     * @Route("/location/new", name="location_new")
     */
    public function locationNewAction(Request $request)
    {
        if ($newLocation = $request->request->get('new')) {
            $locationService = $this->get('AppBundle\Service\LocationService');

            $locationService->saveLocation($newLocation);
        }

        return $this->render('admin/location/new.html.twig', []);
    }

    /**
     * Save a existing location
     *
     * @Route("/location/edit/{id}", name="location_edit")
     */
    public function locationEditAction(Request $request, Location $location)
    {
        if ($editLocation = $request->request->get('edit')) {
            $locationService = $this->get('AppBundle\Service\LocationService');

            $locationService->saveLocation($editLocation);

            $translated = $this->get('translator')->trans('success.edit');
            $this->addFlash(
                'success',
                $translated . '.'
            );
        }

        return $this->render('admin/location/edit.html.twig', [
            'location'  => $location,
        ]);
    }

    /**
     * Delete a location
     *
     * @Route("/location/delete/{id}", name="location_delete")
     */
    public function locationDeleteAction(Request $request, Location $location)
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findEntriesByLocation($location);

        foreach ($entries as $entry) {
            $entry->setLocation(null);
            $em->persist($entry);
        }

        $em->remove($location);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_index'));
    }

    /**
     * List all tags
     *
     * @Route("/tag/", name="tag_index")
     */
    public function tagIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        return $this->render('admin/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * Save a new tag
     *
     * @Route("/tag/new", name="tag_new")
     */
    public function tagNewAction(Request $request)
    {
        if ($newTag = $request->request->get('new')) {
            $tagService = $this->get('AppBundle\Service\TagService');

            $tagService->saveTag($newTag);
        }

        return $this->render('admin/tag/new.html.twig', []);
    }

    /**
     * Save a existing tag
     *
     * @Route("/tag/edit/{id}", name="tag_edit")
     */
    public function tagEditAction(Request $request, Tag $tag)
    {
        if ($editTag = $request->request->get('edit')) {
            $tagService = $this->get('AppBundle\Service\TagService');

            $tagService->saveTag($editTag);

            $translated = $this->get('translator')->trans('success.edit');
            $this->addFlash(
                'success',
                $translated . '.'
            );
        }

        return $this->render('admin/tag/edit.html.twig', [
            'tag'  => $tag,
        ]);
    }

    /**
     * Delete a tag
     *
     * @Route("/tag/delete/{id}", name="tag_delete")
     */
    public function tagDeleteAction(Request $request, Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($tag);
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
        $authors = $em->getRepository('AppBundle:Author')->findAll();
        $locations = $em->getRepository('AppBundle:Location')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        return $this->render('admin/inc/sidebar.html.twig', [
            'entries' => $entries,
            'authors'  => $authors,
            'locations' => $locations,
            'tags' => $tags,
        ]);
    }
}
