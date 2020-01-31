<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Tour;
use AppBundle\Service\CoreService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TourController extends Controller
{
    /**
     * @Route("/tour/page/{page}", name="tour_index_paginated", requirements={"page": "\d+"})
     */
    public function indexAction(CoreService $coreService, $page): Response
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Tour')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query, Tour::PAGINATION_QUANTITY);

        if ($page > 1 && $page > $pages) {
            throw new NotFoundHttpException();
        }

        $tours = PaginationHelper::paginate($query, Tour::PAGINATION_QUANTITY, $page);

        foreach ($tours as $tour) {
            $coreService->setGpxData($tour);
        }

        return $this->render('frontend/tour/index.html.twig', [
            'tours' => $tours,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/tour/{slug}", name="tour_show")
     * @ParamConverter("tour", class="AppBundle:Tour", options={"repository_method" = "findOneByCriteria"})
     */
    public function showAction(CoreService $coreService, Tour $tour, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $coreService->setGpxData($tour);

        if (!$tour->getEntries()->isEmpty()) {
            $locations = array_map(function ($e) { return $e->getLocation(); }, $tour->getEntries()->toArray());
            $locations = array_unique($locations);
        }

        $page = $em->getRepository('AppBundle:Tour')->findTourListPageNumber($tour);

        $breadcrumbs = [
            [
                'url' => $this->generateUrl('tour_index_paginated', ['page' => $page]),
                'name' => $translator->trans('tours'),
            ],
            [
                'url' => $this->generateUrl('tour_show', ['slug' => $tour->getSlug()]),
                'name' => $tour->getName(),
            ],
        ];

        return $this->render('frontend/tour/show.html.twig', [
            'tour' => $tour,
            'locations' => $locations ?? [],
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
