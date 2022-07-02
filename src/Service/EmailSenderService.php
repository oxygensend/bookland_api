<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailSenderService
{

    public function __construct(private  readonly MailerInterface $mailer,
                                private  readonly VerifyEmailHelperInterface $verifyEmailHelper)
    {
    }

    public function sendMail(User $data): void
    {

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'api_users_registration_confirmation_item',
            $data->getId(),
            $data->getEmail(),
            ['id' => $data->getId()]
        );


        $message = (new Email())
            ->from('bookland@gmail.com')
            ->to($data->getEmail())
            ->subject('Email verification')
            ->text(sprintf('Click link below and finish registration process.\n%s',
                $signatureComponents->getSignedUrl()
            ));

        $this->mailer->send($message);
    }

}