<?php

namespace App\Entity;

use App\Enum\ContinentEnum;
use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: City::class, cascade: ['persist'])]
    private Collection $cities;

    public function __construct(
        #[ORM\Column(length: 255)]
        private ?string $name,
        #[ORM\Column(length: 2)]
        private ?string $alpha2 = null,
        #[ORM\Column(length: 3)]
        private ?string $alpha3 = null,
        #[ORM\Column(type: Types::INTEGER, length: 3)]
        private ?string $num = null,
        #[ORM\Column(type: Types::INTEGER, length: 10)]
        private ?string $isd = null,
        #[ORM\Column(type: Types::STRING, enumType: ContinentEnum::class)]
        private null|ContinentEnum $continent = null,
        #[ORM\ManyToOne]
        private null|City $capitalCity = null
    ) {
        $this->cities = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAlpha2(): ?string
    {
        return $this->alpha2;
    }

    public function setAlpha2(?string $alpha2): void
    {
        $this->alpha2 = $alpha2;
    }

    public function getAlpha3(): ?string
    {
        return $this->alpha3;
    }

    public function setAlpha3(?string $alpha3): void
    {
        $this->alpha3 = $alpha3;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(?string $num): void
    {
        $this->num = $num;
    }

    public function getIsd(): ?string
    {
        return $this->isd;
    }

    public function setIsd(?string $isd): void
    {
        $this->isd = $isd;
    }

    public function getContinent(): ?ContinentEnum
    {
        return $this->continent;
    }

    public function setContinent(?ContinentEnum $continent): void
    {
        $this->continent = $continent;
    }

    public function getCapitalCity(): ?City
    {
        return $this->capitalCity;
    }

    public function setCapitalCity(?City $capitalCity): void
    {
        $this->capitalCity = $capitalCity;
    }

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): static
    {
        if (! $this->cities->contains($city)) {
            $this->cities->add($city);
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(City $city): static
    {
        $this->cities->removeElement($city);
        return $this;
    }
}
