<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PageRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function page(PageRepository $pageRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'pages' => $pageRepository->findOneBy(['slug'=>'accueil'])
        ]);
    }
}
