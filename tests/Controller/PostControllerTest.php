<?php

namespace App\Test\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PostRepository $repository;
    private string $path = '/crud/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Post::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Post index');

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
            'post[picture]' => 'Testing',
            'post[content]' => 'Testing',
            'post[likes]' => 'Testing',
            'post[date_post]' => 'Testing',
            'post[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/crud/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Post();
        $fixture->setPicture('My Title');
        $fixture->setContent('My Title');
        $fixture->setLikes('My Title');
        $fixture->setDate_post('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Post');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Post();
        $fixture->setPicture('My Title');
        $fixture->setContent('My Title');
        $fixture->setLikes('My Title');
        $fixture->setDate_post('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'post[picture]' => 'Something New',
            'post[content]' => 'Something New',
            'post[likes]' => 'Something New',
            'post[date_post]' => 'Something New',
            'post[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/crud/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPicture());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getLikes());
        self::assertSame('Something New', $fixture[0]->getDate_post());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Post();
        $fixture->setPicture('My Title');
        $fixture->setContent('My Title');
        $fixture->setLikes('My Title');
        $fixture->setDate_post('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/crud/');
    }
}
