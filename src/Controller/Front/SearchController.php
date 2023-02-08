<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Elastica\Query\MultiMatch;
use Elastica\Query\BoolQuery;
use Elastica\Query;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function page(Request $request, Client $client): JsonResponse
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);

        $match = new MultiMatch();
        $match->setQuery($query);
        $match->setFields(["title", "content"]);

        $bool = new BoolQuery();
        $bool->addMust($match);

        $elasticaQuery = new Query($bool);
        $elasticaQuery->setSize($limit);

        $foundPages = $client->getIndex('page')->search($elasticaQuery);
        $results = [];
        foreach ($foundPages as $post) {
            $results[] = $post->getSource();
        }


        return $this->json($results, 200);
    }
}
