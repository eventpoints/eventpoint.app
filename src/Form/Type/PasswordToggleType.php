<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class PasswordToggleType extends AbstractType
{
    #[\Override]
    public function getParent(): string
    {
        return PasswordType::class;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'password_toggle';
    }
}
