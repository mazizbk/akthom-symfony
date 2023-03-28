<?php
// src/EventSubscriber/FilePdfSubscriber.php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use App\Service\ElasticService;
use App\Entity\PdfFile;

class FilePdfSubscriber implements EventSubscriberInterface
{
    private $elasticsearch;
    private $logger;

    public function __construct(ElasticService $elasticsearch, LoggerInterface $logger)
    {
        $this->elasticsearch = $elasticsearch;
        $this->logger = $logger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate

        ];
    }


    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logger->info('Mise à jour des méta-données debut !');

        $entity = $args->getObject();

        if ($entity instanceof PdfFile) {
            $filename = $entity->getFilename();
            $metadata = $entity->getMetadata();
            $this->logger->info('Mise à jour des méta-données effectuée !');

            $this->elasticsearch->updateMetadata($filename, $metadata);
        }
    }
}
