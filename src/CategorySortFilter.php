<?php

namespace App;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class CategorySortFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if($this->isPropertyEnabled($property, $resourceClass))
            return;

        switch ($value){

            case "popular":
                $queryBuilder->orderBy('o.popularityRate', 'DESC');
                break;
            case "ASC":
                $queryBuilder->orderBy('o.title', 'ASC');
                break;
            case "DESC":
                $queryBuilder->orderBy('o.title', 'DESC');
                break;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        if(!$this->properties){
            return [];
        }
        $property = array_keys($this->properties)[0];
        $description[$property] = [
            'property' => $property,
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'swagger' => [
                'description' => 'Filter items, possible options:\npopular - by popularityRate\n
                     ASC, DESC - alphabetically',
                'name' => 'CategorySortFilter',
                'type' => 'String'
            ],
        ];


        return $description;

    }


}