<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/admin', name: 'app_home_admin')]
    public function index(): Response
    {
        return $this->render('admin/home/index.html.twig', [
                    'controller_name' => 'HomeAdminController',
                ]);
    }
}
