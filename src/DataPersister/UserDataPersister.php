<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private readonly ContextAwareDataPersisterInterface $decoratedDataPersister,
                                private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {

        if (($data->getPlainPassword() && $data->getPasswordConfirmation()) &&
            $data->getPlainPassword() === $data->getPasswordConfirmation()) {

            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }

        $this->decoratedDataPersister->persist($data);

    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data);
    }

}