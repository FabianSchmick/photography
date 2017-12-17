<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PaginationHelper;
use AppBundle\Entity\Entry;
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
     * Gather all information for the entry detail page
     *
     * @Route("/entry/{slug}", name="entry_detail")
     */
    public function entryDetailAction(Request $request, Entry $entry)
    {
        return $this->render('frontend/inc/entry.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Gather all information for the ajax entry detail page (lightbox)
     *
     * @Route("/ajax/entry/{id}/", name="ajax_entry_detail")
     */
    public function ajaxEntryDetailAction(Request $request, Entry $entry)
    {
        return $this->render('frontend/inc/ajaxEntry.html.twig', [
            'entry' => $entry,
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
