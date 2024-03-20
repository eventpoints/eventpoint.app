<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\EventGroup\EventGroup;
use App\Form\Form\EventGroup\EventGroupFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent('event_group_form')]
class EventGroupComponent extends AbstractController
{
    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp(fieldName: 'data')]
    public null|EventGroup $item = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EventGroupFormType::class, $this->item);
    }
}
