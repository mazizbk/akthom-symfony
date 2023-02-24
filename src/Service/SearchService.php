<?php
// src/Service/SearchService.php

namespace App\Service;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;

class SearchService
{
    private Client $client;

    public function __construct(string $elasticsearchHost)
    {
        $this->client = ClientBuilder::create()->setHosts([$elasticsearchHost])->build();
    }

    public function search(string $index, string $searchString, bool $isPerfect = false, string $language = 'en-GB'): array
    {
        $keywords = preg_split("/[\s,]+/", $searchString);

        if ($isPerfect) {


            $query = [
                'bool' => [
                    'must' => [
                        'match_phrase' => [
                            'content' => [
                                'query' => \strtolower($searchString),
                                'slop' => 0
                            ]
                        ]

                    ]

                ]
            ];
        } else {
            $clauses = $this->buildSearchClauses($keywords);

            $query = [
                "span_near" => [
                    "clauses" => $clauses,
                    "slop" => 3,
                    "in_order" => true
                ]
            ];
        }
        $params = [
            'index' => $index,
            'body' => [
                'query' => $query,
                'highlight' => [
                    'pre_tags' => ["<mark>"],
                    'post_tags' => ["</mark>"],
                    'fields' => [
                        'content' => new \stdClass()
                    ],
                    'require_field_match' => true
                ],
            ]
        ];

        $results = $this->client->search($params);
        return $results;
    }

    private function buildSearchClauses(array $keywords): array
    {

        $clauses = [];
        foreach ($keywords as $word) {
            $clauses[] = [
                "span_multi" => [
                    "match" => [
                        "fuzzy" => [
                            "content" => [
                                "value" => strtolower($word),
                                "boost" => '1.0',
                                "prefix_length" => 0,
                                "max_expansions" => 10000
                            ],


                        ]
                    ]
                ]
            ];
        }

        return $clauses;
    }
}
