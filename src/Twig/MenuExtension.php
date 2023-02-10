<?php

// src/Twig/MenuExtension.php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use App\Service\MenuService;

class MenuExtension extends AbstractExtension
{
    private $menuService;
    private $twig;

    public function __construct(MenuService $menuService, Environment $twig)
    {
        $this->menuService = $menuService;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('render_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
        ];
    }

    public function renderMenu()
    {
        $menuItems = $this->menuService->getMenuItems();

        return $this->twig->render('parts/_menu.html.twig', [
            'menu_items' => $menuItems
        ]);
    }
}
