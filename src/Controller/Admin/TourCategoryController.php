<?php

namespace App\Controller\Admin;

use App\Entity\TourCategory;
use App\Form\TourCategoryType;
use App\Repository\TourCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * TourCategory controller.
 *
 * @Route("admin/tour-category")
 */
class TourCategoryController extends AbstractController
{
    /**
     * List all tourCategories.
     *
     * @Route("/", name="admin_tour_category_index", methods={"GET"})
     */
    public function index(TourCategoryRepository $tourCategoryRepository): Response
    {
        return $this->render('admin/tour-category/index.html.twig', [
            'tourCategories' => $tourCategoryRepository->findAll(),
        ]);
    }

    /**
     * Save a new tourCategory.
     *
     * @Route("/new", name="admin_tour_category_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $tourCategory = new TourCategory();
        $form = $this->createForm(TourCategoryType::class, $tourCategory);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tourCategory);
            $em->flush();

            $url = $this->generateUrl('admin_tour_category_edit', ['id' => $tourCategory->getId()]);

            $translated = $translator->trans('flash.success.new');
            $this->addFlash(
                'success',
                $translated.': <a class="alert-link" href="'.$url.'">'.$tourCategory->getName().'</a>.'
            );

            return $this->redirectToRoute('admin_tour_category_new');
        }

        return $this->render('admin/tour-category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Save a existing tourCategory.
     *
     * @Route("/edit/{id}", name="admin_tour_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TourCategory $tourCategory): Response
    {
        $form = $this->createForm(TourCategoryType::class, $tourCategory);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tourCategory);
            $em->flush();

            $this->addFlash('success', 'flash.success.edit');

            return $this->redirectToRoute('admin_tour_category_edit', ['id' => $tourCategory->getId()]);
        }

        return $this->render('admin/tour-category/edit.html.twig', [
            'tourCategory' => $tourCategory,
            'form' => $form->createView(),
            'deleteForm' => $this->createDeleteForm($tourCategory)->createView(),
        ]);
    }

    /**
     * Delete a tourCategory.
     *
     * @Route("/delete/{id}", name="admin_tour_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TranslatorInterface $translator, TourCategory $tourCategory): RedirectResponse
    {
        $form = $this->createDeleteForm($tourCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($tourCategory);
            $em->flush();

            $translated = str_replace('%tour_category%', $tourCategory->getName(), $translator->trans('flash.success.deleted.tour_category'));
            $this->addFlash('success', $translated);
        } else {
            $this->addFlash('danger', 'flash.error.deleted');
        }

        return $this->redirectToRoute('admin_index');
    }

    private function createDeleteForm(TourCategory $tourCategory): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_tour_category_delete', ['id' => $tourCategory->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}