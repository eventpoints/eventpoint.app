<?php

declare(strict_types=1);

namespace App\Service\EmailService;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EmailService
{
    private const SENDER_EMAIL_ADDRESS = 'notifications@eventpoint.app';

    public function __construct(
        private MailerInterface $mailer,
        private TranslatorInterface $translator
    ) {
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
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendEventParticipantInvitationEmail(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: 'email.event-invitation.subject',
            template: '/email/event-invitation-email.html.twig',
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendEventOrgniserInvitationEmail(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: 'email.event-crew-invitation.subject',
            template: '/email/event-crew-invitation-email.html.twig',
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendInviteToUserWithoutAccount(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: $this->translator->trans('email.invitation.subject'),
            template: '/email/no-account-participant-invitation-email.html.twig',
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendInviteToUserWithAccount(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: $this->translator->trans('email.invitation.subject'),
            template: '/email/invitation-email.html.twig',
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendMessageRecivedEmail(string $recipientEmailAddress, array $context = []): void
    {
        $this->send(
            subject: 'email.contact-email.subject',
            template: '/email/contact-email.html.twig',
            recipientEmailAddress: $recipientEmailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     */
    private function compose(
        string $subject,
        string $template,
        string $recipientEmailAddress,
        array  $context
    ): TemplatedEmail {
        $email = new TemplatedEmail();
        $email->from(addresses: self::SENDER_EMAIL_ADDRESS);
        $email->to(address: new Address($recipientEmailAddress));
        $email->subject(subject: $subject);
        $email->htmlTemplate(template: $template);
        $email->context(context: $context);
        return $email;
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    private function send(
        string $subject,
        string $template,
        string $recipientEmailAddress,
        array  $context
    ): void {
        try {
            $email = $this->compose(
                subject: $subject,
                template: $template,
                recipientEmailAddress: $recipientEmailAddress,
                context: $context
            );
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $transportException) {
            throw new $transportException();
        }
    }
}
