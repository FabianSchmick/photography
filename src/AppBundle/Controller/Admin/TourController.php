<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tour;
use AppBundle\Form\TourType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Tour controller.
 *
 * @Route("admin/tour")
 */
class TourController extends Controller
{
    /**
     * List all tours.
     *
     * @Route("/", name="admin_tour_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tours = $em->getRepository('AppBundle:Tour')->findAll();

        return $this->render('admin/tour/index.html.twig', [
            'tours' => $tours,
        ]);
    }

    /**
     * Save a new tour.
     *
     * @Route("/new", name="admin_tour_new")
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $tour = new Tour();
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tour);
            $em->flush();

            $url = $this->generateUrl('admin_tour_edit', ['id' => $tour->getId()]);

            $translated = $translator->trans('success.new');
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
    public function editAction(Request $request, TranslatorInterface $translator, Tour $tour)
    {
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tour);
            $em->flush();

            $translated = $translator->trans('success.edit');
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
    public function deleteAction(TranslatorInterface $translator, Tour $tour)
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%tour%', $tour->getName(), $translator->trans('success.deleted.tour'));

        $em->remove($tour);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
