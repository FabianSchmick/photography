<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Tour;
use App\Repository\TourRepository;
use App\Service\CoreService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TourController extends AbstractController
{
    /**
     * @Route("/tour/page/{page}", name="tour_index", requirements={"page": "\d+"})
     */
    public function index(CoreService $coreService, TourRepository $tourRepository, $page): Response
    {
        $query = $tourRepository->getFindAllQuery();
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
     * @Entity("tour", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(CoreService $coreService, Tour $tour, TourRepository $tourRepository, TranslatorInterface $translator): Response
    {
        $coreService->setGpxData($tour);

        if (!$tour->getEntries()->isEmpty()) {
            $locations = array_map(function ($e) { return $e->getLocation(); }, $tour->getEntries()->toArray());
            $locations = array_unique($locations);
        }

        $page = $tourRepository->findTourListPageNumber($tour);

        $breadcrumbs = [
            [
                'url' => $this->generateUrl('tour_index', ['page' => $page]),
                'name' => $translator->trans('header.tours'),
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
