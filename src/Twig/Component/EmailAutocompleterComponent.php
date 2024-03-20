<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\Event;
use App\Entity\User\Email;
use App\Entity\User\User;
use App\Entity\User\UserContact;
use App\Factory\EmailFactory;
use App\Factory\UserContactFactory;
use App\Repository\Event\EventRepository;
use App\Repository\User\EmailRepository;
use App\Repository\User\UserContactRepository;
use App\Service\EventService\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('email_autocompleter_component')]
class EmailAutocompleterComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    #[Assert\Email]
    public null|string $emailAddress = null;

    #[LiveProp(writable: true)]
    public null|Event $event = null;

    /**
     * @var array<int, UserContact>
     */
    public array $contacts = [];

    public function __construct(
        private readonly UserContactRepository $userContactRepository,
        private readonly EmailRepository $emailRepository,
        private readonly UserContactFactory $userContactFactory,
        private readonly EmailFactory $emailFactory,
        private readonly EventService $eventService,
        private readonly EventRepository $eventRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<int, UserContact>
     */
    public function getContacts(#[CurrentUser] User $currentUser): array
    {
        $this->contacts = $this->userContactRepository->findByOwner(user: $currentUser, limit: 30);
        return $this->contacts;
    }

    #[LiveAction]
    public function filter(#[CurrentUser] User $currentUser): void
    {
        $this->contacts = $this->userContactRepository->findByOwnerAndQuery(user: $currentUser, emailAddress: $this->emailAddress, limit: 30);
    }

    #[LiveAction]
    public function submit(#[CurrentUser] User $currentUser): void
    {
        $email = $this->createEmail(emailAddress: $this->emailAddress);
        $this->createUserContact(email: $email, user: $currentUser);
        $this->eventRepository->save($this->event, true);
        $this->contacts = $this->userContactRepository->findByOwnerAndQuery(user: $currentUser, emailAddress: $this->emailAddress, limit: 30);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[LiveAction]
    public function sendInvitation(#[CurrentUser] User $currentUser, #[LiveArg] UserContact $contact): void
    {
        $this->eventService->process(event: $this->event, email: $contact->getEmail(), currentUser: $currentUser, requestStack: $this->requestStack);
        $this->eventRepository->save($this->event, true);
        $this->contacts = $this->userContactRepository->findByOwnerAndQuery(user: $currentUser, emailAddress: $this->emailAddress, limit: 30);
    }

    private function createUserContact(Email $email, User $user): UserContact
    {
        $contact = $this->userContactRepository->findOneBy([
            'email' => $email,
            'owner' => $user,
        ]);

        if (! $contact instanceof UserContact) {
            $contact = $this->userContactFactory->create(email: $email, owner: $user);
        }

        $this->userContactRepository->save($contact, true);
        return $contact;
    }

    private function createEmail(string $emailAddress): Email
    {
        $email = $this->emailRepository->findOneBy([
            'address' => $emailAddress,
        ]);

        if (! $email instanceof Email) {
            $email = $this->emailFactory->create(emailAddress: $emailAddress);

            if ($email->getOwner() instanceof User) {
                $email->setOwner($email->getOwner());
            }
        }

        return $email;
    }
}
