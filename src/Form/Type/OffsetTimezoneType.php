<?php

declare(strict_types=1);

namespace App\Form\Type;

use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffsetTimezoneType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getTimezoneChoices(),
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * @return array<string, string>
     * @throws \Exception
     */
    private function getTimezoneChoices(): array
    {
        $timezones = DateTimeZone::listIdentifiers();
        $choices = [];

        foreach ($timezones as $timezone) {
            $dateTimeZone = new DateTimeZone($timezone);
            $offset = $dateTimeZone->getOffset(new DateTime());
            $offsetFormatted = sprintf('%+03d:%02d', $offset / 3600, abs($offset) % 3600 / 60);
            $choices[$offsetFormatted] = $dateTimeZone->getName();
        }

        ksort($choices);
        return $choices;
    }
}
