<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function indexAction(): RedirectResponse
    {
        // Till I don't know what to display here, redirect to new entry
        return $this->redirect($this->generateUrl('admin_entry_new'));
//        return $this->render('admin/index.html.twig', []);
    }

    /**
     * Renders the admin navigation sidebar.
     */
    public function renderSidebarAction(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Entry')->findBy([], ['timestamp' => 'DESC']);
        $authors = $em->getRepository('AppBundle:Author')->findBy([], ['name' => 'ASC']);
        $locations = $em->getRepository('AppBundle:Location')->findBy([], ['name' => 'ASC']);
        $tags = $em->getRepository('AppBundle:Tag')->findBy([], ['sort' => 'DESC']);
        $tours = $em->getRepository('AppBundle:Tour')->findBy([], ['updated' => 'DESC']);

        return $this->render('admin/inc/sidebar.html.twig', [
            'entries' => $entries,
            'authors' => $authors,
            'locations' => $locations,
            'tags' => $tags,
            'tours' => $tours,
        ]);
    }
}
