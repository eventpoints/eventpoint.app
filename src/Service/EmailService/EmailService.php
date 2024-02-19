<?php

declare(strict_types=1);

namespace App\Service\EmailService;

use App\Entity\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EmailService
{
    private const SENDER_EMAIL_ADDRESS = 'notifications@eventpoint.app';

    public function __construct(
        private MailerInterface     $mailer,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendRegistrationWelcomeEmail(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: 'email.registration-welcome-email.subject',
            template: '/email/registration-email.html.twig',
            email: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendEventParticipantInvitationEmail(Email $email, array $context = []): void
    {
        $this->send(
            subject: 'email.event-invitation.subject',
            template: '/email/event-invitation-email.html.twig',
            email: $email,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendEventOrgniserInvitationEmail(Email $email, array $context = []): void
    {
        $this->send(
            subject: 'email.event-crew-invitation.subject',
            template: '/email/event-crew-invitation-email.html.twig',
            email: $email,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendInviteToUserWithoutAccount(Email $email, array $context = []): void
    {
        $this->send(
            subject: $this->translator->trans('email.invitation.subject'),
            template: '/email/no-account-participant-invitation-email.html.twig',
            email: $email,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendInviteToUserWithAccount(Email $email, array $context = []): void
    {
        $this->send(
            subject: $this->translator->trans('email.invitation.subject'),
            template: '/email/invitation-email.html.twig',
            email: $email,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendMessageRecivedEmail(Email $email, array $context = []): void
    {
        $this->send(
            subject: 'email.contact-email.subject',
            template: '/email/contact-email.html.twig',
            email: $email,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     */
    private function compose(
        string $subject,
        string $template,
        string  $emailAddress,
        array  $context
    ): TemplatedEmail
    {
        $templatedEmail = new TemplatedEmail();
        $templatedEmail->from(addresses: self::SENDER_EMAIL_ADDRESS);
        $templatedEmail->to(address: new Address($emailAddress));
        $templatedEmail->subject(subject: $subject);
        $templatedEmail->htmlTemplate(template: $template);
        $templatedEmail->context(context: $context);
        return $templatedEmail;
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    private function send(
        string $subject,
        string $template,
        Email  $email,
        array  $context
    ): void
    {
        try {
            $envelope = $this->compose(
                subject: $subject,
                template: $template,
                emailAddress: $email->getAddress(),
                context: $context
            );
            $this->mailer->send($envelope);
        } catch (TransportExceptionInterface $transportException) {
            throw new $transportException();
        }
    }
}
