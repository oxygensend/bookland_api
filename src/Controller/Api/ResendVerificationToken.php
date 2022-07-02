<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\EmailSenderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResendVerificationToken extends AbstractController
{

    public function __invoke(User $data, EmailSenderService $emailSender): JsonResponse
    {
        if($data->getEmailConfirmedAt() !== null)
            return $this->json(['error' => 'Your email address has been confirmed'],400);

        $emailSender->sendMail($data);

        return $this->json(['success' => 'Verification link has been sent. Please check your email'], 200);
    }
}