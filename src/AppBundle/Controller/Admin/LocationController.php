<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Location;
use AppBundle\Form\LocationType;
use AppBundle\Service\LocationService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Location controller.
 *
 * @Route("admin/location")
 */
class LocationController extends Controller
{
    /**
     * List all locations.
     *
     * @Route("/", name="admin_location_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locations = $em->getRepository('AppBundle:Location')->findAll();

        return $this->render('admin/location/index.html.twig', [
            'locations' => $locations,
        ]);
    }

    /**
     * Save a new location.
     *
     * @Route("/new", name="admin_location_new")
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $url = $this->generateUrl('admin_location_edit', ['id' => $location->getId()]);

            $translated = $translator->trans('success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$location->getName().'</a>.'
            );

            $form = $this->createForm(LocationType::class, new Location());
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
    public function editAction(Request $request, LocationService $locationService, TranslatorInterface $translator, Location $location)
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $translated = $translator->trans('success.edit');
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
    public function deleteAction(Request $request, Location $location)
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
}
