<?php

namespace App\Controller\Admin;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PageRepository;
use App\Form\PageType;
use App\Entity\Page;

#[Route('/admin/page')]
class PageController extends AbstractController
{
    #[Route('/', name: 'app_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PageRepository $pageRepository, SluggerInterface $slugger): Response
    {
        $page = new Page();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug($slugger->slug($page->getTitle()));
            $pageRepository->save($page, true);

            return $this->redirectToRoute('app_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'admin_page_show', methods: ['GET'])]
    public function show(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(['slug'=>$slug]);
        return $this->render('admin/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, PageRepository $pageRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug($slugger->slug($page->getTitle()));
            $pageRepository->save($page, true);

            return $this->redirectToRoute('app_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $pageRepository->remove($page, true);
        }

        return $this->redirectToRoute('app_page_index', [], Response::HTTP_SEE_OTHER);
    }
}
