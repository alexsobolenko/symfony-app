<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppAssertsTest extends WebTestCase
{
    /**
     * @var array
     */
    private array $data = [
        [
            'id' => null,
            'name' => 'М. Фрай',
            'books' => [
                [
                    'id' => null,
                    'name' => 'Лабиринты Ехо',
                    'price' => 2.0,
                ],
                [
                    'id' => null,
                    'name' => 'Хроники Ехо',
                    'price' => 3.0,
                ],
                [
                    'id' => null,
                    'name' => 'Сновидения Ехо',
                    'price' => 4.3,
                ],
            ],
        ],
        [
            'id' => null,
            'name' => 'Р. Кнаак',
            'books' => [
                [
                    'id' => null,
                    'name' => 'Кровавое наследие',
                    'price' => 5.1,
                ],
                [
                    'id' => null,
                    'name' => 'Королевство теней',
                    'price' => 6.6,
                ],
            ],
        ],
    ];

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    public function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->entityManager = $entityManager;

        /** @var UrlGeneratorInterface $router */
        $router = self::getContainer()->get('router');
        $this->router = $router;
    }

    public function testApp()
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(302);

        $client->request('GET', $this->router->generate('app_authors_list'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Authors');

        $client->request('GET', $this->router->generate('app_author_create'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Add new author');

        for ($i = 0; $i < count($this->data); $i++) {
            $author = new Author($this->data[$i]['name']);
            $this->entityManager->persist($author);
            $this->entityManager->flush();
            $this->data[$i]['id'] = $author->getId();

            for ($j = 0; $j < count($this->data[$i]['books']); $j++) {
                $book = new Book($author, $this->data[$i]['books'][$j]['name'], $this->data[$i]['books'][$j]['price']);
                $this->entityManager->persist($book);
                $this->entityManager->flush();
                $this->data[$i]['books'][$j]['id'] = $book->getId();
            }
        }

        $crawler = $client->request('GET', $this->router->generate('app_authors_list'));
        $this->assertResponseStatusCodeSame(200);

        $crawler->filter('li.list-group-item')->each(function(Crawler $node, $i) {
            $text = $node->filter('div:first-child')->eq(0)->text();
            $value = $this->data[$i]['name'] . ' (' . count($this->data[$i]['books']) . ')';
            $this->assertSame($text, $value);
        });

        foreach ($this->data as $authorItem) {
            $client->request('GET', $this->router->generate('app_author_edit', [
                'id' => $authorItem['id'],
            ]));
            $this->assertResponseStatusCodeSame(200);
            $this->assertSelectorTextContains('h1', 'Edit ' . $authorItem['name']);
        }

        $crawler = $client->request('GET', $this->router->generate('app_books_list'));
        $this->assertResponseStatusCodeSame(200);

        $books = [];
        foreach ($this->data as $a) {
            foreach ($a['books'] as $b) {
                $books[] = [
                    'id' => $b['id'],
                    'name' => $b['name'],
                    'price' => $b['price'],
                    'author' => $a['name'],
                ];
            }
        }

        $crawler->filter('li.list-group-item')->each(function(Crawler $node, $i) use ($books) {
            $text = $node->filter('div:first-child')->eq(0)->text();
            $value = $books[$i]['name'] . ' (' . $books[$i]['author'] . ') $' . $books[$i]['price'];
            $this->assertSame($text, $value);
        });

        foreach ($books as $book) {
            $client->request('GET', $this->router->generate('app_book_edit', [
                'id' => $book['id'],
            ]));
            $this->assertResponseStatusCodeSame(200);
            $this->assertSelectorTextContains('h1', 'Edit ' . $book['name'] . ' (' . $book['author'] . ')');
        }

        /** @var AuthorRepository $authorRepo */
        $authorRepo = $this->entityManager->getRepository(Author::class);

        /** @var BookRepository $bookRepo */
        $bookRepo = $this->entityManager->getRepository(Book::class);

        foreach ($this->data[0]['books'] as $bookItem) {
            $book = $bookRepo->find($bookItem['id']);
            $this->entityManager->remove($book);
            $this->entityManager->flush();
        }

        $crawler = $client->request('GET', $this->router->generate('app_authors_list'));
        $this->assertResponseStatusCodeSame(200);

        $crawler->filter('li.list-group-item')->each(function(Crawler $node, $i) {
            $text = $node->filter('div:first-child')->eq(0)->text();
            $cnt = $i === 0 ? 0 : count($this->data[$i]['books']);
            $value = $this->data[$i]['name'] . ' (' . $cnt . ')';
            $this->assertSame($text, $value);
        });

        $author = $authorRepo->find($this->data[1]['id']);
        $this->entityManager->remove($author);
        $this->entityManager->flush();

        $client->request('GET', $this->router->generate('app_authors_list'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('li.list-group-item', $this->data[0]['name'] . ' (0)');
        $this->entityManager->flush();

        $client->request('GET', $this->router->generate('app_books_list'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('li.list-group-item', 'Books not found');
    }
}
