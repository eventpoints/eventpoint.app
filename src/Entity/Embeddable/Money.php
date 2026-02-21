<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Enum\CurrencyCodeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Money implements \Stringable
{
    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $amount = null;

    public function __construct(
        ?int $amount = null,
        #[ORM\Column(length: 3, nullable: true)]
        #[Assert\Currency]
        private string $currency = CurrencyCodeEnum::CZK->value
    ) {
        $this->amount = $amount !== null ? (string) $amount : null;
    }

    public function getAmount(): ?int
    {
        return $this->amount !== null ? (int) $this->amount : null;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount !== null ? (string) $amount : null;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function isZero(): bool
    {
        return $this->amount === null || (int) $this->amount === 0;
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Returns the amount in the base unit (e.g., 10.00 for 1000 cents)
     */
    public function getBaseUnit(): ?float
    {
        if ($this->amount === null) {
            return null;
        }

        $fractionDigits = Currencies::getFractionDigits($this->currency);
        $divisor = 10 ** $fractionDigits;

        return (int) $this->amount / $divisor;
    }

    public function __toString(): string
    {
        if ($this->amount === null) {
            return '';
        }

        $baseUnit = $this->getBaseUnit();

        return number_format($baseUnit, 2, ',', ' ') . ' ' . $this->currency;
    }
}
