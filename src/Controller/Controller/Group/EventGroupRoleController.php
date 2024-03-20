<?php

declare(strict_types=1);

namespace App\Controller\Controller\Group;

use App\Entity\EventGroup\EventGroupMember;
use App\Entity\EventGroup\EventGroupRole;
use App\Entity\User\User;
use App\Enum\EventGroupRoleEnum;
use App\Enum\FlashEnum;
use App\Form\Form\EventGroup\EventGroupMemberRoleFormType;
use App\Repository\EventGroup\EventGroupMemberRepository;
use App\Repository\EventGroup\EventGroupRoleRepository;
use App\Security\Voter\EventGroupVoter;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/groups/roles')]
class EventGroupRoleController extends AbstractController
{
    public function __construct(
        private readonly EventGroupMemberRepository $eventGroupMemberRepository,
        private readonly EventGroupRoleRepository $eventGroupRoleRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/edit/{id}', name: 'edit_group_member_roles', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(EventGroupMember $eventGroupMember, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $this->isGranted(EventGroupVoter::ADD_GROUP_MEMBER, $eventGroupMember->getEventGroup());

        $eventGroupMemberRoleForm = $this->createForm(EventGroupMemberRoleFormType::class, $eventGroupMember);
        $eventGroupMemberRoleForm->handleRequest($request);
        if ($eventGroupMemberRoleForm->isSubmitted() && $eventGroupMemberRoleForm->isValid()) {
            $maintainerRole = $this->eventGroupRoleRepository->findOneBy([
                'title' => EventGroupRoleEnum::ROLE_GROUP_MAINTAINER,
            ]);
            /** @var PersistentCollection $roles */
            $roles = $eventGroupMemberRoleForm->get('roles')->getData();

            if (
                $roles->exists(fn (int $key, EventGroupRole $eventGroupRole) => in_array($eventGroupRole->getTitle(), [
                    EventGroupRoleEnum::ROLE_GROUP_MOD,
                    EventGroupRoleEnum::ROLE_GROUP_MANAGER,
                    EventGroupRoleEnum::ROLE_GROUP_PROMOTER,
                    EventGroupRoleEnum::ROLE_GROUP_SPONSOR,
                ], true))
            ) {
                if (! $roles->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER)) {
                    $roles->add($maintainerRole);
                }
            } else {
                $roles->removeElement($maintainerRole);
            }

            if ($roles->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MEMBER)) {
                $conflictingRoles = [
                    EventGroupRoleEnum::ROLE_GROUP_MOD,
                    EventGroupRoleEnum::ROLE_GROUP_MANAGER,
                    EventGroupRoleEnum::ROLE_GROUP_PROMOTER,
                    EventGroupRoleEnum::ROLE_GROUP_SPONSOR,
                ];

                if ($roles->exists(fn (int $key, EventGroupRole $eventGroupRole) => in_array($eventGroupRole->getTitle(), $conflictingRoles, true))) {
                    $errorMessage = 'Cannot have admin role and member role';
                    $eventGroupMemberRoleForm->get('roles')->addError(new FormError($errorMessage));
                    return $this->render('events/group/members/edit.html.twig', [
                        'eventGroupMember' => $eventGroupMember,
                        'eventGroupMemberRoleForm' => $eventGroupMemberRoleForm,
                    ]);
                }
            }

            $hasMaintainerWithManagerOrCreator = $roles->exists(
                fn (int $key, EventGroupRole $eventGroupRole) =>
                $eventGroupRole->getTitle() === EventGroupRoleEnum::ROLE_GROUP_MAINTAINER &&
                $roles->exists(fn (int $key, EventGroupRole $role) => in_array($role->getTitle(), [
                    EventGroupRoleEnum::ROLE_GROUP_MANAGER,
                    EventGroupRoleEnum::ROLE_GROUP_CREATOR,
                ], true))
            );

            if (! $hasMaintainerWithManagerOrCreator) {
                $errorMessage = 'At least group manager is required';
                $eventGroupMemberRoleForm->get('roles')->addError(new FormError($errorMessage));

                return $this->render('events/group/members/edit.html.twig', [
                    'eventGroupMember' => $eventGroupMember,
                    'eventGroupMemberRoleForm' => $eventGroupMemberRoleForm,
                ]);
            }

            $this->eventGroupMemberRepository->save($eventGroupMember, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('event_group_members', [
                'id' => $eventGroupMember->getEventGroup()->getId(),
            ]);
        }

        return $this->render('events/group/members/edit.html.twig', [
            'eventGroupMember' => $eventGroupMember,
            'eventGroupMemberRoleForm' => $eventGroupMemberRoleForm,
        ]);
    }
}
