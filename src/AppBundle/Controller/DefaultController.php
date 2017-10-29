<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('frontend/index.html.twig', [
        ]);
    }

    /**
     * @Route("/page/{page}", name="paginate_entries", requirements={"page": "\d+"})
     */
    public function paginateEntriesAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Entry')->getFindAllQuery();
        $pages = PaginationHelper::getPagesCount($query);
        $entries = PaginationHelper::paginate($query, 10, $page);

        return $this->render('frontend/inc/entries.html.twig', [
            'entries'   => $entries,
            'page'      => $page,
            'pages'     => $pages
        ]);
    }

    /**
     * Change language
     *
     * @Route("admin/language/{locale}", name="changeLanguageAdmin")
     * @Route("/language/{locale}", name="changeLanguage")
     */
    public function changeLanguageAction($locale)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $routeName = $request->get('_route');
        $request->attributes->set('_locale', null);
        $this->get('session')->set('_locale', $locale);

        $redirect = 'homepage';
        if ($routeName == 'changeLanguageAdmin') {
            $redirect = 'admin_index';
        }

        return $this->redirect($this->generateUrl($redirect));
    }
}
