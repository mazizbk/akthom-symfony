<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ElasticService;



#[Route('/result')]


class ResultController extends AbstractController
{
    #[Route('/search', methods: ['GET', 'POST', 'HEAD'], name: 'app_result')]
    public function search(Request $request, ElasticService $elasticService): Response
    {
        $searchString = $request->query->get('search_string') ?? $request->request->get('search_string');
        $isPerfect = $request->query->get('perfect') ?? $request->request->get('perfect');
        $results = $elasticService->search('pdf_aktehom', $searchString, $isPerfect ? true : false);
        return $this->render('result/index.html.twig', [
            'res' => $results
        ]);


        /*    
        // Paginer les résultats

        $pagination = $paginator->paginate(
            $results['hits']['hits'],
            $request->query->getInt('page', 1),
            10
        );
        
        */
    }
}
