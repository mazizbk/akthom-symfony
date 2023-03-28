<?php
// src/Service/ElasticService.php

namespace App\Service;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;

class ElasticService
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

    public function getMetaByFileName(string $index, string $fileName)
    {

        $params = [
            'index' => $index,
            'body' => [
                "query" => [
                    "match" => [
                        "file.filename" => $fileName
                    ]
                ],
                '_source' => [
                    "includes" => ['meta']
                ]
            ],


        ];

        $results = $this->client->search($params);



        return isset($results['hits']['hits'][0]) ? $results['hits']['hits'][0]['_source']['meta'] : null;
    }
    public function getIdByFileName($filename)
    {


        // Search for document ID
        $params = [
            'index' => 'pdf_aktehom',
            'body' => [
                'query' => [
                    'match' => [
                        'file.filename' => $filename,
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);
        // Update document with new metadata
        return isset($response['hits']['hits'][0]) ? $response['hits']['hits'][0]['_id'] : null;
    }
    public function updateMetadata($filename, $metadata)
    {

        $id = $this->getIdByFileName($filename);
        if (!\is_null($id)) {
            $metadata = \json_decode($metadata, \true);
            $params = [
                'index' => 'pdf_aktehom',
                'id' => $id,
                'body' => [
                    'doc' => [
                        'meta' => $metadata
                    ]
                ]
            ];
            $this->client->update($params);
        }
    }


    public function deleteByFileName($filename)
    {
        $id = $this->getIdByFileName($filename);
        if (!\is_null($id)) {
            $params = [
                'index' => 'pdf_aktehom',
                'id' => $id
            ];

            $this->client->delete($params);
        }
    }
}
