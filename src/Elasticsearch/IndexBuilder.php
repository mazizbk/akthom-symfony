<?php

namespace App\Elasticsearch;

use Symfony\Component\Yaml\Yaml;
use Elastica\Client;

class IndexBuilder
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create()
    {
        // We name our index "blog"
        $index = $this->client->getIndex('page');

        $settings = Yaml::parse(
          file_get_contents(
            __DIR__.'/../../config/elasticsearch_index_page.yaml'
          )
        );

        // We build our index settings and mapping
        $index->create($settings);

        return $index;
    }
}