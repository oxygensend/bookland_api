<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Service\EmailSenderService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SendEmailVerificationSubscriber implements EventSubscriberInterface
{

    public function __construct(private  readonly EmailSenderService $emailSender){}

    #[ArrayShape([KernelEvents::VIEW => "array"])] public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::VIEW => ['sendMail', EventPriorities::POST_WRITE]
        ];
    }

    public function sendMail(ViewEvent $event)
    {
        $data = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$data instanceof User || Request::METHOD_POST !== $method)
            return;


       $this->emailSender->sendMail($data);

    }

}