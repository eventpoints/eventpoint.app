<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Form\DataTransformerInterface;

final class FlowbiteDateTimeTransformer implements DataTransformerInterface
{
    private string $dateFormat;

    private string $timeFormat;

    private ?string $modelTimezone;

    public function __construct(string $dateFormat = 'Y-m-d', string $timeFormat = 'H:i', ?string $modelTimezone = null)
    {
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
        $this->modelTimezone = $modelTimezone;
    }

    /**
     * @param DateTimeInterface|null $value
     * @return array{date:string,time:string}
     */
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
            'time' => $dt->format('H:i:s'),
        ];
    }

    /**
     * @param array<string,string>|null $value
     */
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
