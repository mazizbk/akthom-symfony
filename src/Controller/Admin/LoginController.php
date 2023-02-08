<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class LoginController extends AbstractController
{
    #[Route('/admin/login', name: 'app_admin_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('admin/login/index.html.twig', [
            'last_username' => $lastUsername,
             'error'         => $error,
        ]);
    }
}
