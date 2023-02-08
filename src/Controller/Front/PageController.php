<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PageRepository;

class PageController extends AbstractController
{
    #[Route('/{slug}', name: 'app_front_page')]
    public function page(string $slug,PageRepository $pageRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'pages' => $pageRepository->findOneBy(['slug'=>$slug])
        ]);
    }
}
