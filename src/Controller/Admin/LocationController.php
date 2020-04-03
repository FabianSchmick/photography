<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="admin_location_index")
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
     * @Route("/new", name="admin_location_new")
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

            $translated = $translator->trans('flash.success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$location->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_location_new');
        }

        return $this->render('admin/location/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing location.
     *
     * @Route("/edit/{id}", name="admin_location_edit")
     */
    public function edit(Request $request, TranslatorInterface $translator, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $translated = $translator->trans('flash.success.edit');
            $this->addFlash(
                'success',
                $translated.'.'
            );
        }

        return $this->render('admin/location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a location.
     *
     * @Route("/delete/{id}", name="admin_location_delete")
     */
    public function delete(TranslatorInterface $translator, Location $location): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $translated = str_replace('%location%', $location->getName(), $translator->trans('flash.success.deleted.location'));

        $em->remove($location);
        $em->flush();

        $this->addFlash(
            'success',
            $translated.'.'
        );

        return $this->redirectToRoute('admin_index');
    }
}
