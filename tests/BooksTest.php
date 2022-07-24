<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Book;
use App\Entity\User;
use App\Tests\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class BooksTest extends ApiTestCase
{
    use RefreshDatabaseTrait;


    public function testGetCollection(): void
    {

        $response = static::createClient()->request('GET', '/api/books');

        $response_array = $response->toArray()['hydra:member'];
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(30, $response_array);
        $this->assertJsonContains([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/books?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/books?page=1',
                'hydra:last' => '/api/books?page=4',
                'hydra:next' => '/api/books?page=2',
            ]
        ]);

        $this->assertArrayHasKey('@id',$response_array[0]);
        $this->assertArrayHasKey('@type',$response_array[0]);
        $this->assertArrayHasKey('title', $response_array[0]);
        $this->assertArrayHasKey('price', $response_array[0]);
        $this->assertArrayHasKey('amount', $response_array[0]);
        $this->assertArrayHasKey('slug', $response_array[0]);
        $this->assertArrayHasKey('popularityRate', $response_array[0]);
        $this->assertArrayHasKey('author', $response_array[0]);

    }

    public function testPagination(): void
    {
        $response = static::createClient()->request('GET', '/api/books?page=2');

        $response_array = $response->toArray();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(30, $response_array['hydra:member']);
        $this->assertJsonContains([
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/books?page=2',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/books?page=1',
                'hydra:last' => '/api/books?page=4',
                'hydra:previous' => '/api/books?page=1',
                'hydra:next' => '/api/books?page=3',
            ]
        ]);
    }

    public function testOnlyEnabledBooksAreReturned()
    {
        $response = static::createClient()->request('GET', '/api/books');
        $books = array_column($response->toArray()['hydra:member'], 'slug');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $enabled = count($em->getRepository(Book::class)->findAllEnabledWithSlugInArray($books));

        $this->assertCount($enabled, $books);
    }

    public function testFetchOneBook()
    {

        $book_iri = $this->findIriBy(Book::class, ['id' => 1]);
        $response = static::createClient()->request('GET', $book_iri);
        $response_array = $response->toArray();

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books/1',
            '@type' => 'Book'
        ]);
        $this->assertArrayHasKey('isbn', $response_array);
        $this->assertArrayHasKey('title', $response_array);
        $this->assertArrayHasKey('price', $response_array);
        $this->assertArrayHasKey('amount', $response_array);
        $this->assertArrayHasKey('description', $response_array);
        $this->assertArrayHasKey('publicationDate', $response_array);
        $this->assertArrayHasKey('author', $response_array);


    }
    
}
