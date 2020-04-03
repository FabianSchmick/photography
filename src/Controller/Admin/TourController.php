<?php

namespace App\Controller\Admin;

use App\Entity\Tour;
use App\Form\TourType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Tour controller.
 *
 * @Route("admin/tour")
 */
class TourController extends AbstractController
{
    /**
     * List all tours.
     *
     * @Route("/", name="admin_tour_index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tours = $em->getRepository('App:Tour')->findAll();

        return $this->render('admin/tour/index.html.twig', [
            'tours' => $tours,
        ]);
    }

    /**
     * Save a new tour.
     *
     * @Route("/new", name="admin_tour_new")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tour = new Tour();
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tour);
            $em->flush();

            $url = $this->generateUrl('admin_tour_edit', ['id' => $tour->getId()]);

            $translated = $translator->trans('flash.success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$tour->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_tour_new');
        }

        return $this->render('admin/tour/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing tour.
     *
     * @Route("/edit/{id}", name="admin_tour_edit")
     */
    public function edit(Request $request, TranslatorInterface $translator, Tour $tour): Response
    {
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tour);
            $em->flush();

            $translated = $translator->trans('flash.success.edit');
            $this->addFlash(
                'success',
                $translated.'.'
            );
        }

        return $this->render('admin/tour/edit.html.twig', [
            'tour' => $tour,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a tour.
     *
     * @Route("/delete/{id}", name="admin_tour_delete")
     */
    public function delete(TranslatorInterface $translator, Tour $tour): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%tour%', $tour->getName(), $translator->trans('flash.success.deleted.tour'));

        $em->remove($tour);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
