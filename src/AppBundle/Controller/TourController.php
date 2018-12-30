<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Tour;
use AppBundle\Service\CoreService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TourController extends Controller
{
    /**
     * @Route("/tour/page/{page}", name="tour_index_paginated", requirements={"page": "\d+"})
     */
    public function indexAction(Request $request, CoreService $coreService, $page)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Tour')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query, Tour::PAGINATION_QUANTITY);
        $tours = PaginationHelper::paginate($query, Tour::PAGINATION_QUANTITY, $page);

        if ($page > 1 && $page > $pages) {
            throw new NotFoundHttpException();
        }

        foreach ($tours as $tour) {
            $coreService->setGpxData($tour);
        }

        return $this->render('frontend/tour/index.html.twig', [
            'tours' => $tours,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
