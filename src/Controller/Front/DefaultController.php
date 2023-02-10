<?php
namespace App\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MenuService;

class DefaultController extends AbstractController
{
    public function index(MenuService $menuService): Response
    {
        $menuItems = $menuService->getMenuItems();

        return $this->render('default/index.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }
}
