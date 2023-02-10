<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController
{
    #[Route('/faq', name: 'app_faq')]
    public function index(): Response
    {
        return $this->render('faq/index.html.twig', [
            'controller_name' => 'FaqController',
        ]);
    }
}
