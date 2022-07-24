<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Category;

class CategoryDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(readonly private CollectionDataProviderInterface $collectionDataProvider)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        /** @var Category[] $categories */
        $categories = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        foreach ($categories as $category) {
            $name = $category->getName();
            $tree = $this->buildTree($name, $category->getParent());
            $category->setTree($tree);
        }
        return $categories;

    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Category::class && $context['groups'] === 'category:getAll' || $context['groups'] === 'category:getOne';
    }

    private function buildTree(&$name = '', Category $parent = null): string
    {
        if ($parent !== null) {
            $name = $parent->getName() . " > " . $name;
            if ($parent->getParent() !== null) {
                $this->buildTree($name, $parent->getParent());
            }
        }
        return $name;
    }

}