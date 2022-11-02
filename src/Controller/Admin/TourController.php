<?php

namespace App\Controller\Admin;

use App\Entity\Tour;
use App\Form\TourType;
use App\Repository\TourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * List all tours.
     *
     * @Route("/", name="admin_tour_index", methods={"GET"})
     */
    public function index(TourRepository $tourRepository): Response
    {
        return $this->render('admin/tour/index.html.twig', [
            'tours' => $tourRepository->findAll(),
        ]);
    }

    /**
     * Save a new tour.
     *
     * @Route("/new", name="admin_tour_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tour = new Tour();
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tour);
            $this->entityManager->flush();

            $url = $this->generateUrl('admin_tour_edit', ['id' => $tour->getId()]);

            $translated = $translator->trans('flash.success.new', [
                '%link%' => $url,
                '%name%' => $tour->getName(),
            ]);

            $this->addFlash('success', $translated);

            return $this->redirectToRoute('admin_tour_new');
        }

        return $this->render('admin/tour/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing tour.
     *
     * @Route("/edit/{id}", name="admin_tour_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tour $tour): Response
    {
        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tour);
            $this->entityManager->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_tour_edit', ['id' => $tour->getId()]);
        }

        return $this->render('admin/tour/edit.html.twig', [
            'tour' => $tour,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($tour)->createView(),
        ]);
    }

    /**
     * Delete a tour.
     *
     * @Route("/delete/{id}", name="admin_tour_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, Tour $tour): RedirectResponse
    {
        $form = $this->createDeleteForm($tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($tour);
            $this->entityManager->flush();

            $translated = $translator->trans('flash.success.deleted.tour', [
                '%tour%' => $tour->getName(),
            ]);

            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Tour $tour): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_tour_delete', ['id' => $tour->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
