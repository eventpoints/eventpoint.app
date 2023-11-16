<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Event\Event;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
final class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $dataUrl = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ImageCollection $imageCollection;

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getDataUrl(): ?string
    {
        return $this->dataUrl;
    }

    public function setDataUrl(?string $dataUrl): static
    {
        $this->dataUrl = $dataUrl;

        return $this;
    }
    public function getImageCollection(): ?ImageCollection
    {
        return $this->imageCollection;
    }

    public function setImageCollection(?ImageCollection $imageCollection): static
    {
        $this->imageCollection = $imageCollection;

        return $this;
    }
}
