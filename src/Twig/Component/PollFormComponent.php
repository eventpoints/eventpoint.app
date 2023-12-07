<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Poll\Poll;
use App\Form\Form\Poll\PollFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent('poll_form')]
class PollFormComponent extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public null|Poll $poll = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            PollFormType::class,
            $this->poll
        );
    }
}
