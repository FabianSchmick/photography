<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET","POST"})
     * https://stackoverflow.com/questions/35663410/form-builder-for-symfony-login-page
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $defaultData = ['_username' => $authenticationUtils->getLastUsername()];

        $form = $this->createForm(LoginType::class, $defaultData);

        if (!is_null($authenticationUtils->getLastAuthenticationError(false))) {
            $form->addError(new FormError(
                $authenticationUtils->getLastAuthenticationError()->getMessageKey()
            ));
        }

        $form->handleRequest($request);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
