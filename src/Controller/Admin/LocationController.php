<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Location controller.
 *
 * @Route("admin/location")
 */
class LocationController extends AbstractController
{
    /**
     * List all locations.
     *
     * @Route("/", name="admin_location_index", methods={"GET"})
     */
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('admin/location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * Save a new location.
     *
     * @Route("/new", name="admin_location_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $url = $this->generateUrl('admin_location_edit', ['id' => $location->getId()]);

            $translated = $translator->trans('flash.success.new', [
                '%link%' => $url,
                '%name%' => $location->getName(),
            ]);

            $this->addFlash('success', $translated);

            return $this->redirectToRoute('admin_location_new');
        }

        return $this->render('admin/location/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing location.
     *
     * @Route("/edit/{id}", name="admin_location_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_location_edit', ['id' => $location->getId()]);
        }

        return $this->render('admin/location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($location)->createView(),
        ]);
    }

    /**
     * Delete a location.
     *
     * @Route("/delete/{id}", name="admin_location_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, Location $location): RedirectResponse
    {
        $form = $this->createDeleteForm($location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($location);
            $em->flush();

            $translated = $translator->trans('flash.success.deleted.location', [
                '%location%' => $location->getName(),
            ]);

            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(Location $location): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_location_delete', ['id' => $location->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
