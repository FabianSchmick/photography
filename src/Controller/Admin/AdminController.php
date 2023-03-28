<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Location;
use App\Entity\Tag;
use App\Entity\Tour;
use App\Entity\TourCategory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
        // Till I don't know what to display here, redirect to new post
        return $this->redirect($this->generateUrl('admin_post_new'));
//        return $this->render('admin/index.html.twig', []);
    }

    /**
     * Renders the admin navigation sidebar.
     */
    public function renderSidebar(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findBy([], ['timestamp' => 'DESC']);
        $user = $em->getRepository(User::class)->findBy([], ['fullname' => 'ASC']);
        $locations = $em->getRepository(Location::class)->findBy([], ['name' => 'ASC']);
        $tags = $em->getRepository(Tag::class)->findBy([], ['sort' => 'DESC']);
        $tours = $em->getRepository(Tour::class)->findBy([], ['sort' => 'DESC', 'updated' => 'DESC']);
        $tourCategories = $em->getRepository(TourCategory::class)->findBy([], ['sort' => 'DESC']);

        return $this->render('admin/inc/sidebar.html.twig', [
            'posts' => $posts,
            'users' => $user,
            'locations' => $locations,
            'tags' => $tags,
            'tours' => $tours,
            'tourCategories' => $tourCategories,
        ]);
    }
}
