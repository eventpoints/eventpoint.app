<?php

declare(strict_types=1);

namespace App\Form\Form\Image;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImageFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('images', DropzoneType::class, [
            'label' => $this->translator->trans('drag-and-drop-image-upload-placeholder'),
            'attr' => [
                'placeholder' => $this->translator->trans('drag-and-drop-image-upload-placeholder'),
            ],
            'multiple' => true,
            'mapped' => false,
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
