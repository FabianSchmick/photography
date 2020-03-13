<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{_locale}", requirements={"_locale": "%app.locales%"})
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('frontend/index.html.twig');
    }
}
