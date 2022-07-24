<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;

class CategoiresTest extends ApiTestCase
{
    public function testGetCollection()
    {
        $response = static::createClient()->request('GET', '/api/categories');
        $categories = $response->toArray()['hydra:member'];

        $this->assertResponseStatusCodeSame(200);

        $this->assertArrayHasKey('@id', $categories[0]);
        $this->assertArrayHasKey('@type', $categories[0]);
        $this->assertArrayHasKey('name', $categories[0]);
        $this->assertArrayHasKey('slug', $categories[0]);
        $this->assertArrayHasKey('categories', $categories[0]);
        $this->assertArrayHasKey('tree', $categories[0]);
        $this->assertArrayHasKey('parent', $categories[0]);
        $this->assertArrayHasKey('image_path', $categories[0]);

        $this->assertJsonContains([
            '@context' => '/api/contexts/Category',
            '@id' => '/api/categories',
            '@type' => 'hydra:Collection',
        ]);

    }


    public function testGetOne()
    {
        $response = static::createClient()->request('GET', '/api/categories/1');

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var Category $category */
        $category = $em->getRepository(Category::class)->find(1);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
            'name' => $category->getName(),
            'parent' => $category->getParent(),
            'tree' => $category->getTree()
        ]);
    }

}
