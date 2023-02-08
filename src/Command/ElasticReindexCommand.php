<?php
namespace App\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Elasticsearch\PageIndexer;
use App\Elasticsearch\IndexBuilder;

#[AsCommand(name: 'elastic:reindex')]

class ElasticReindexCommand extends Command
{
    //protected static $defaultName = 'elastic:reindex';

    private $indexBuilder;
    private $pageIndexer;

    public function __construct(IndexBuilder $indexBuilder, PageIndexer $pageIndexer)
    {
        $this->indexBuilder = $indexBuilder;
        $this->pageIndexer = $pageIndexer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Rebuild the Index and populate it.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $index = $this->indexBuilder->create();

        $io->success('Index created!');

        $this->pageIndexer->indexAllDocuments($index->getName());

        $io->success('Index populated and ready!');
    }
}
