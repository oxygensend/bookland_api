<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    private $client;

    #[ORM\OneToOne(targetEntity: Order::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $orderI;

    #[ORM\ManyToOne(targetEntity: PaymentMethods::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private $paymentMethod;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOrderI(): ?Order
    {
        return $this->orderI;
    }

    public function setOrderI(Order $orderI): self
    {
        $this->orderI = $orderI;

        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethods
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethods $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}
