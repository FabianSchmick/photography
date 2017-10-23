<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
use AppBundle\Service\EntryService;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        return $this->render('admin/index.html.twig', [

        ]);
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
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        if ($newEntry = $request->request->get('new')) {
            $image = $request->files->get('new');

            $entryService = $this->get('AppBundle\Service\EntryService');

            $entryService->saveEntry($newEntry, $image);
        }

        return $this->render('admin/entry/new.html.twig', [
            'tags' => $tags,
        ]);
    }
}
