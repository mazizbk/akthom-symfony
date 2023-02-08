<?php

namespace App\Elasticsearch;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Elastica\Document;
use Elastica\Client;
use App\Repository\PageRepository;
use App\Entity\Page;

class PageIndexer
{
    private $client;
    private $pageRepository;
    private $router;

    public function __construct(Client $client, PageRepository $pageRepository, UrlGeneratorInterface $router)
    {
        $this->client = $client;
        $this->pageRepository = $pageRepository;
        $this->router = $router;
    }

    public function buildDocument(Page $page)
    {
        return new Document(
            $page->getId(), // Manually defined ID
            [
                'title' => $page->getTitle(),
                'description' => $page->getDescription(),
                'content' => $page->getContent(),

                // Not indexed but needed for display
                'url' => $this->router->generate('app_front_page', ['slug' => $page->getSlug()], UrlGeneratorInterface::ABSOLUTE_PATH),
            ],
            "page" // Types are deprecated, to be removed in Elastic 7
        );
    }

    public function indexAllDocuments($indexName)
    {
        $allPages = $this->pageRepository->findAll();
        $index = $this->client->getIndex($indexName);

        $documents = [];
        foreach ($allPages as $page) {
            $documents[] = $this->buildDocument($page);
        }

        $index->addDocuments($documents);
        $index->refresh();
    }
}