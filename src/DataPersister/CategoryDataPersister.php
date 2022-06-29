<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Category;

class CategoryDataPersister implements DataPersisterInterface
{
    private DataPersisterInterface $decoratedDataPersister;

    public function __construct(DataPersisterInterface $decoratedDataPersister)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
    }

    public function supports($data): bool
    {
        return $data instanceof Category;
    }

    public function persist($data)
    {


    }

    public function remove($data)
    {
        // TODO: Implement remove() method.
    }

}