<?php

// src/Service/MenuService.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Menu;

class MenuService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getMenuItems()
    {
        return $this->entityManager->getRepository(Menu::class)->findBy([], ['id' => 'ASC']);
    }
}
