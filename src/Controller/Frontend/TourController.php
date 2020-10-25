<?php

namespace App\Controller\Frontend;

use App\Doctrine\PaginationHelper;
use App\Entity\Tour;
use App\Repository\TourCategoryRepository;
use App\Repository\TourRepository;
use App\Service\TourService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class TourController extends AbstractController
{
    /**
     * @Route("/tour/page/{page}", name="tour_index", requirements={"page": "\d+"})
     */
    public function index(Request $request, TourService $tourService, TourRepository $tourRepository, TourCategoryRepository $categoryRepository, $page): Response
    {
        if ($activeCategory = $request->query->get('category')) {
            $activeCategory = $categoryRepository->find($activeCategory);
        }

        $query = $tourRepository->getFindAllQuery($activeCategory);
        $pages = PaginationHelper::getPagesCount($query, Tour::PAGINATION_QUANTITY);

        if ($page > 1 && $page > $pages) {
            throw new NotFoundHttpException();
        }

        $tours = PaginationHelper::paginate($query, Tour::PAGINATION_QUANTITY, $page);

        foreach ($tours as $tour) {
            $tourService->setGpxData($tour);
        }

        return $this->render('frontend/tour/index.html.twig', [
            'tours' => $tours,
            'categories' => $categoryRepository->findBy([], ['sort' => 'DESC']),
            'activeCategory' => $activeCategory,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/tour/map", name="tour_map")
     */
    public function map(Request $request, TourService $tourService, TourRepository $tourRepository, TourCategoryRepository $categoryRepository, UploaderHelper $uploaderHelper): Response
    {
        if ($activeCategory = $request->query->get('category')) {
            $activeCategory = $categoryRepository->find($activeCategory);
        }

        /** @var Tour[] $tours */
        $tours = $tourRepository->getFindAllQuery($activeCategory)->getResult();

        $mapData = [];
        foreach ($tours as $tour) {
            $tourService->setGpxData($tour);
            $mapData[$tour->getId()] = $uploaderHelper->asset($tour->getFile(), 'file');
        }

        return $this->render('frontend/tour/map.html.twig', [
            'tours' => $tours,
            'mapData' => $mapData,
            'categories' => $categoryRepository->findBy([], ['sort' => 'DESC']),
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * Route getting the marker popup content.
     *
     * @Route("/ajax/tour/map/marker-popup/{tour}", name="tour_marker_popup_ajax", condition="request.isXmlHttpRequest()")
     */
    public function ajaxMarkerPopup(TourService $tourService, Tour $tour): Response
    {
        $tourService->setGpxData($tour);

        return $this->render('frontend/tour/ajax-marker-popup.html.twig', [
            'tour' => $tour,
        ]);
    }

    /**
     * @Route("/tour/{slug}", name="tour_show")
     * @Entity("tour", expr="repository.findOneByCriteria(_locale, {'slug': slug})")
     */
    public function show(Request $request, TourService $tourService, Tour $tour, TourRepository $tourRepository, TourCategoryRepository $categoryRepository, TranslatorInterface $translator, UploaderHelper $uploaderHelper): Response
    {
        $tourService->setGpxData($tour);
        $mapData = [$tour->getId() => $uploaderHelper->asset($tour->getFile(), 'file')];

        if ($activeCategoryId = $request->query->get('category')) {
            $activeCategory = $categoryRepository->find($activeCategoryId);
        }

        $page = $tourRepository->findTourListPageNumber($tour, $activeCategory ?? null);

        $breadcrumbs = [
            [
                'url' => $this->generateUrl('tour_index', ['page' => $page, 'category' => $activeCategoryId]),
                'name' => $translator->trans('header.tours'),
            ],
            [
                'url' => $this->generateUrl('tour_show', ['slug' => $tour->getSlug()]),
                'name' => $tour->getName(),
            ],
        ];

        return $this->render('frontend/tour/show.html.twig', [
            'tour' => $tour,
            'mapData' => $mapData,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
