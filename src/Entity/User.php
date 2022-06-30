<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use App\Validator\IsPasswordConfirmed;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['post' => ["path" => "register"]],
    itemOperations: ['get', 'patch', 'delete'],
    denormalizationContext: ['groups' => 'addNew'],
)]
#[IsPasswordConfirmed]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Serializer\Groups(['addNew'])]
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    #[ORM\Column(type: 'string', length: 100)]
    #[Serializer\Groups(['addNew'])]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Name have to be at least 2 characters",
        maxMessage: "Name have to be no longer than 50 characters")]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 100)]
    #[Serializer\Groups(['addNew'])]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Surname have to be at least 2 characters",
        maxMessage: "Surname have to be no longer than 50 characters")]
    #[Assert\NotBlank]
    private string $surname;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
        message: 'Password have to be minimum 8 characters and contains at least one letter and number.'

    )]
    #[Assert\Length(min: 8, minMessage: "Password have to be at least 8 characters")]
    private ?string $plainPassword;

    #[Assert\NotBlank]
    private string $password_confirmation;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private string $phone;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class, orphanRemoval: true)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        parent::__construct();
        $this->addresses = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setClient($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getClient() === $this) {
                $payment->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }


    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }


    #[Serializer\Groups(['addNew'])]
    #[Serializer\SerializedName('password')]
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }


    public function getPasswordConfirmation(): string
    {
        return $this->password_confirmation;
    }


    #[Serializer\Groups(['addNew'])]
    public function setPasswordConfirmation(string $password_confirmation): void
    {
        $this->password_confirmation = $password_confirmation;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }


}
