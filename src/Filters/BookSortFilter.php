<?php

namespace App\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class BookSortFilter extends AbstractContextAwareFilter
{


    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!$this->isPropertyEnabled($property, $resourceClass))
            return;

        switch ($value) {

            case "popular":
                $queryBuilder->orderBy('o.popularityRate', 'DESC');
                break;
            case "priceAsc":
                $queryBuilder->orderBy('o.price', 'ASC');
                break;
            case "priceDesc":
                $queryBuilder->orderBy('o.price', 'DESC');
                break;
            case "newest":
                $queryBuilder->orderBy('o.createdAt', 'DESC');
                break;
            case "a-Z":
                $queryBuilder->orderBy('o.title', 'ASC');
                break;
            case "Z-a":
                $queryBuilder->orderBy('o.title', 'DESC');
                break;

        }


    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $property = array_keys($this->properties)[0];
        $description[$property] = [
            'property' => $property,
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'swagger' => [
                'description' => 'Filter items, possible options:\npopular - by popularityRate\n
                     priceAsc, priceDesc - price ASC/DESC\n
                     newest - by newest added book\n
                     a-Z, Z-a - alphabetically',
                'name' => 'BookSortFilter',
                'type' => 'String'
            ],
        ];


        return $description;
    }

}