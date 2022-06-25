<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\Groups;


abstract class AbstractEntity
{
    use TimestampableEntity;


    public function __construct() {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    #[Id]
    #[GeneratedValue]
    #[Column(type: "integer")]
    #[Groups("withId")]
    protected $id;


    public function getId()
    {
        return $this->id;
    }


}