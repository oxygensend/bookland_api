<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsController]
class EmailVerification extends AbstractController
{

    public function __invoke(User $data, Request $request, VerifyEmailHelperInterface $verifyEmailHelper ): JsonResponse
    {
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $data->getId(),
                $data->getEmail());
        }  catch(VerifyEmailExceptionInterface $e){

            return $this->json(['error' => $e->getReason(), $e->getCode()]);
        }

        return  $this->json(['success' => 'Your e-mail address has been verified.']);
    }
}