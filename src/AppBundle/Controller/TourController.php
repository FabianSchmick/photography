<?php

namespace AppBundle\Controller;

use AppBundle\Service\CoreService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}/tour", requirements={"_locale": "%app.locales%"})
 */
class TourController extends Controller
{
    /**
     * @Route("/", name="tour_index")
     */
    public function indexAction(Request $request, CoreService $coreService)
    {
        $em = $this->getDoctrine()->getManager();

        $tours = $em->getRepository('AppBundle:Tour')->findBy([], ['updated' => 'DESC']);

        foreach ($tours as $tour) {
            $coreService->setGpxData($tour);
        }

        return $this->render('frontend/tour/index.html.twig', [
            'tours' => $tours,
        ]);
    }
}
