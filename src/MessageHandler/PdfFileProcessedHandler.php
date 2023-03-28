<?php

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Client as GuzzelClient;
use App\Service\ElasticService;
use App\Repository\PdfFileRepository;
use App\Event\PdfFileProcessedEvent;
use App\Entity\PdfFile;

#[AsMessageHandler]

class PdfFileProcessedHandler
{

    private GuzzelClient $httpClient;
    private string $pdfAktehomDirectory;
    private string $pdfAktehomIndexName;
    private ElasticService $elasticService;
    private PdfFileRepository $pdfFileRepository;

    public function __construct(GuzzelClient $httpClient, string $pdfAktehomDirectory, string $pdfAktehomIndexName, ElasticService $elasticService, PdfFileRepository $pdfFileRepository)
    {
        $this->httpClient = $httpClient;
        $this->pdfAktehomDirectory = $pdfAktehomDirectory;
        $this->pdfAktehomIndexName = $pdfAktehomIndexName;
        $this->elasticService = $elasticService;
        $this->pdfFileRepository = $pdfFileRepository;
    }

    public function __invoke(PdfFileProcessedEvent $event): void
    {
        $filename = $event->getFilename();
        $file = $this->pdfAktehomDirectory . '/' . $filename;

        $stream = new MultipartStream([
            [
                'name' => 'file',
                'contents' => fopen($file, 'r'),
            ],
            [
                'name' => 'index',
                'contents' => $this->pdfAktehomIndexName,
            ],
        ]);

        $request = new GuzzleRequest('POST', 'http://127.0.0.1:8080/fscrawler/_upload', [], $stream);

        $this->httpClient->send($request);
        $pdfFile = $this->pdfFileRepository->findOneBy(['filename' => $filename]);
        $metadata = json_encode($this->elasticService->getMetaByFileName('pdf_aktehom', $filename));
        $pdfFile->setMetadata($metadata);

        $this->pdfFileRepository->save($pdfFile, true);
    }
}
