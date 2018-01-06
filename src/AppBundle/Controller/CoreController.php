<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    /**
     * @Route("/", name="locale")
     * @Route("/admin/language/{_locale}/",
     *     defaults={"_locale": "%locale%"},
     *     requirements={"_locale": "%app.locales%"},
     *     name="localeAdmin"
     *  )
     */
    public function localeAction(Request $request)
    {
        $this->get('session')->set('_locale', $this->container->getParameter('locale'));

        if ($requestedLocale = $request->attributes->get('_locale')) {
            $this->get('session')->set('_locale', $requestedLocale);
        }

        $redirect = 'homepage';
        if ($request->get('_route') == 'localeAdmin') {
            $redirect = 'admin_index';
        }

        return $this->redirect($this->generateUrl($redirect));
    }
}
