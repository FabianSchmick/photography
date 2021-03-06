<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Admin controller.
 *
 * @Route("admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(): RedirectResponse
    {
        // Till I don't know what to display here, redirect to new entry
        return $this->redirect($this->generateUrl('admin_entry_new'));
//        return $this->render('admin/index.html.twig', []);
    }

    /**
     * Renders the admin navigation sidebar.
     */
    public function renderSidebar(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('App:Entry')->findBy([], ['timestamp' => 'DESC']);
        $user = $em->getRepository('App:User')->findBy([], ['fullname' => 'ASC']);
        $locations = $em->getRepository('App:Location')->findBy([], ['name' => 'ASC']);
        $tags = $em->getRepository('App:Tag')->findBy([], ['sort' => 'DESC']);
        $tours = $em->getRepository('App:Tour')->findBy([], ['sort' => 'DESC', 'updated' => 'DESC']);
        $tourCategories = $em->getRepository('App:TourCategory')->findBy([], ['sort' => 'DESC']);

        return $this->render('admin/inc/sidebar.html.twig', [
            'entries' => $entries,
            'users' => $user,
            'locations' => $locations,
            'tags' => $tags,
            'tours' => $tours,
            'tourCategories' => $tourCategories,
        ]);
    }
}
