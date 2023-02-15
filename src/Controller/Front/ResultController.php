<?php

namespace App\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Elasticsearch\ClientBuilder;



#[Route('/result')]


class ResultController extends AbstractController
{
    #[Route('/search', methods: ['POST', 'HEAD'], name: 'app_result')]
    public function search(Request $request): Response
    {
        $queryString = $request->request->get('search_string');


        $keywords = preg_split("/[\s,]+/", $queryString);

        $client = ClientBuilder::create()->setHosts(['http://127.0.0.1:9200'])->build();
        $clauses = [];
        foreach ($keywords as $word) {
            $clauses[] = [
                "span_multi" => [
                    "match" => [
                        "fuzzy" => [
                            "content" => [
                                "fuzziness" => "2",
                                "value" => \strtolower($word),
                                "boost" => '1.0',
                                "prefix_length" => 0,
                                "max_expansions" => 100
                            ]
                        ]
                    ]
                ]
            ];
        }

        $params = [
            'index' => 'pdf_aktehom',
            'body'  => [
                "query" => [
                    "span_near" => [
                        "clauses" => $clauses,
                        "slop" => 3,
                        "in_order" => true
                    ]
                ],
                'highlight' => [
                    'pre_tags' => ["<mark>"], // not required
                    'post_tags' => ["</mark>"], // not required
                    'fields' => [
                        'content' => new \stdClass()
                    ],
                    'require_field_match' => true
                ]

            ]
        ];


        $results = $client->search($params);


        return $this->render('result/index.html.twig', [
            'res' => $results,
        ]);
    }
}
