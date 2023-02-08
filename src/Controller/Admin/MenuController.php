<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\MenuRepository;
use App\Form\MenuType;
use App\Entity\Menu;

#[Route('/admin/menu')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'app_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('admin/menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuRepository $menuRepository): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuRepository->save($menu, true);

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }   


        return $this->render('admin/menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuRepository $menuRepository): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuRepository->save($menu, true);

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, MenuRepository $menuRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $menuRepository->remove($menu, true);
        }

        return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
