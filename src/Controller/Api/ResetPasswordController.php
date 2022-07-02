<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    public function __construct( private readonly EntityManagerInterface $em,
                                 private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(User $data): JsonResponse
    {
        $newPassword = $this->randPassword(10);
        $data->setPassword($this->passwordHasher->hashPassword($data, $newPassword));
        $this->em->persist($data);
        $this->em->flush();

        return $this->json(['new_password' => $newPassword], 200);

    }
    private  function randPassword( $length ): string
    {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);

    }

}
