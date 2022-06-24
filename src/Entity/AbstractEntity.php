<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use phpDocumentor\Reflection\Types\Integer;


abstract class AbstractEntity
{
    use TimestampableEntity;

    #[Id]
    #[GeneratedValue]
    #[Column(type: "integer")]
    protected $id;


    public function getId()
    {
        return $this->id;
    }


}