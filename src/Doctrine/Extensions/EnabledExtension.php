<?php

namespace App\Doctrine\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Book;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;

class EnabledExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->andWhere($queryBuilder,$resourceClass);
    }

    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {

        if( Category::class !== $resourceClass && Book::class !== $resourceClass )
            return;


        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.enabled=true', $rootAlias));


    }

}