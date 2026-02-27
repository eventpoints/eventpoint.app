<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Form\DataTransformerInterface;

final readonly class FlowbiteDateTimeTransformer implements DataTransformerInterface
{
    public function __construct(
        private string $dateFormat = 'Y-m-d',
        private string $timeFormat = 'H:i',
        private ?string $modelTimezone = null
    ) {
    }

    /**
     * @param DateTimeInterface|null $value
     * @return array{date:string,time:string}
     */
    #[\Override]
    public function transform($value): array
    {
        if (! $value instanceof DateTimeInterface) {
            return [
                'date' => '',
                'time' => '',
            ];
        }

        $dt = $value instanceof DateTimeImmutable ? $value : DateTimeImmutable::createFromMutable($value);

        if ($this->modelTimezone) {
            $dt = $dt->setTimezone(new DateTimeZone($this->modelTimezone));
        }

        return [
            'date' => $dt->format($this->dateFormat),
            'time' => $dt->format($this->timeFormat),
        ];
    }

    /**
     * @param array<string,string>|null $value
     */
    #[\Override]
    public function reverseTransform($value): ?DateTimeImmutable
    {
        if (! is_array($value)) {
            return null;
        }

        $date = isset($value['date']) ? trim($value['date']) : '';
        $time = isset($value['time']) ? trim($value['time']) : '';

        if ($date === '' && $time === '') {
            return null;
        }
        if ($date !== '' && $time === '') {
            $time = '00:00:00';
        }

        // TimeType with input='string' always returns seconds (H:i:s),
        // so always parse with seconds included
        $format = $this->dateFormat . ' H:i:s';
        $tz = $this->modelTimezone ? new DateTimeZone($this->modelTimezone) : null;

        $dt = $tz instanceof \DateTimeZone
            ? DateTimeImmutable::createFromFormat($format, $date . ' ' . $time, $tz)
            : DateTimeImmutable::createFromFormat($format, $date . ' ' . $time);

        return $dt ?: null;
    }
}
