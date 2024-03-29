<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\DataTransferObject\EventFilterDto;
use App\Entity\Event\Event;
use App\Entity\EventGroup\EventGroup;
use App\Enum\CountryCodeEnum;
use App\Form\Filter\EventFilterType;
use App\Model\RegionalConfiguration;
use App\Repository\CountryRepository;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('event_search_filter', defaultAction: 'search')]
class EventSearchFilterComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public null|EventFilterDto $eventFilterDto = null;

    /**
     * @var Event[]
     */
    public array $events = [];

    #[LiveProp]
    public null|string $jsonEvents = null;

    /**
     * @var EventGroup[]
     */
    public array $groups = [];

    public function __construct(
        private readonly EventGroupRepository $eventGroupRepository,
        private readonly EventRepository $eventRepository,
        private readonly SerializerInterface $serializer,
        private readonly CountryRepository $countryRepository,
        private readonly RegionalConfiguration $regionalConfiguration,
    ) {
    }

    public function instantiateForm(): FormInterface
    {
        $this->eventFilterDto = new EventFilterDto();
        $browserCountryCode = $this->regionalConfiguration->getBrowserRegionalData()?->getCountryCode();

        if (! empty($browserCountryCode)) {
            $country = $this->countryRepository->findOneBy([
                'alpha2' => CountryCodeEnum::tryFrom($browserCountryCode)->value,
            ]);
        } else {
            $country = $this->countryRepository->findOneBy([
                'alpha2' => CountryCodeEnum::CzechRepublic,
            ]);
        }

        $this->eventFilterDto->setCountry($country);
        $this->eventFilterDto->setCity($country->getCapitalCity());
        $events = $this->eventRepository->findByFilter(eventFilterDto: $this->eventFilterDto);
        $this->jsonEvents = $this->serializer->serialize(data: $events, format: JsonEncoder::FORMAT);
        $this->events = $events;
        $this->groups = $this->eventGroupRepository->findByEventFilter(eventFilterDto: $this->eventFilterDto);
        return $this->createForm(EventFilterType::class, $this->eventFilterDto);
    }

    #[LiveAction]
    public function search(): void
    {
        $this->submitForm();
        $events = $this->eventRepository->findByFilter(eventFilterDto: $this->eventFilterDto);
        $this->jsonEvents = $this->serializer->serialize(data: $events, format: JsonEncoder::FORMAT);
        $this->events = $events;
        $this->groups = $this->eventGroupRepository->findByEventFilter(eventFilterDto: $this->eventFilterDto);
    }
}
