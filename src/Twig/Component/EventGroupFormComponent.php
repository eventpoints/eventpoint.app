<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User\User;
use App\Enum\CountryCodeEnum;
use App\Enum\EventGroupRoleEnum;
use App\Form\Form\EventGroup\EventGroupFormType;
use App\Model\RegionalConfiguration;
use App\Repository\CountryRepository;
use App\Repository\Event\EventGroupRepository;
use App\Repository\EventGroup\EventGroupRoleRepository;
use App\Service\ImageUploadService\ImageService;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('event_group_form_component')]
class EventGroupFormComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public null|EventGroup $eventGroup = null;

    public function __construct(
        private readonly RegionalConfiguration $regionalConfiguration,
        private readonly CountryRepository $countryRepository,
        private readonly ImageService $imageUploadService,
        private readonly EventGroupRepository $eventGroupRepository,
        private readonly EventGroupRoleRepository $eventGroupRoleRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[LiveAction]
    public function submit(#[CurrentUser] User $currentUser): void
    {
        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $image = $this->form->get('image')->getData();
            $this->eventGroup->setBase64Image($this->imageUploadService->processAvatar($image)->getEncoded());

            $eventGroupMember = new EventGroupMember(owner: $currentUser, eventGroup: $this->eventGroup, approvedAt: new CarbonImmutable());
            $eventGroupMaintainerRole = $this->eventGroupRoleRepository->findOneBy([
                'title' => EventGroupRoleEnum::ROLE_GROUP_MAINTAINER,
            ]);
            $eventGroupCreatorRole = $this->eventGroupRoleRepository->findOneBy([
                'title' => EventGroupRoleEnum::ROLE_GROUP_CREATOR,
            ]);

            $eventGroupMember->addRole($eventGroupCreatorRole);
            $eventGroupMember->addRole($eventGroupMaintainerRole);
            $this->eventGroup->addEventGroupMember($eventGroupMember);
            $this->eventGroupRepository->save(entity: $this->eventGroup, flush: true);

            $this->addFlash('message', $this->translator->trans(''));
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->eventGroup = new EventGroup();

        $browserCountryCode = $this->regionalConfiguration->getBrowserRegionalData()?->getCountryCode();

        if (! empty($browserCountryCode)) {
            $country = $this->countryRepository->findOneBy([
                'alpha2' => CountryCodeEnum::tryFrom($browserCountryCode)->value,
            ]);
        } else {
            $country = $this->countryRepository->findOneBy([
                'alpha2' => CountryCodeEnum::Azerbaijan,
            ]);
        }
        $this->eventGroup->setCountry($country);

        return $this->createForm(EventGroupFormType::class, $this->eventGroup);
    }
}
