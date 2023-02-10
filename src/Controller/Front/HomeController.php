<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function page()
    {

        return $this->redirectToRoute('app_front_page',['slug'=>'Accueil']);
        
    }
}
