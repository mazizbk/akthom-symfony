<?php

namespace App\Test\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MenuRepository $repository;
    private string $path = '/menu/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Menu::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'menu[label]' => 'Testing',
            'menu[description]' => 'Testing',
            'menu[parent_id]' => 'Testing',
            'menu[page_id]' => 'Testing',
        ]);

        self::assertResponseRedirects('/menu/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Menu();
        $fixture->setLabel('My Title');
        $fixture->setDescription('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setPage_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Menu();
        $fixture->setLabel('My Title');
        $fixture->setDescription('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setPage_id('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'menu[label]' => 'Something New',
            'menu[description]' => 'Something New',
            'menu[parent_id]' => 'Something New',
            'menu[page_id]' => 'Something New',
        ]);

        self::assertResponseRedirects('/menu/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLabel());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getParent_id());
        self::assertSame('Something New', $fixture[0]->getPage_id());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Menu();
        $fixture->setLabel('My Title');
        $fixture->setDescription('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setPage_id('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/menu/');
    }
}
