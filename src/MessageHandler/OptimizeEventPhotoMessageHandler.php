<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\OptimizeEventPhotoMessage;
use App\Repository\Image\ImageRepository;
use App\Service\ImageUploadService\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsMessageHandler]
final readonly class OptimizeEventPhotoMessageHandler
{
    public function __construct(
        private ImageRepository $imageRepository,
        private ImageOptimizer $optimizer,
        private StorageInterface $storage,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(OptimizeEventPhotoMessage $message): void
    {
        $image = $this->imageRepository->find($message->imageId);

        if ($image === null || $image->getImageName() === null) {
            return;
        }

        $absolutePath = $this->storage->resolvePath($image, 'imageFile');

        if ($absolutePath === null || ! file_exists($absolutePath)) {
            return;
        }

        $optimized = $this->optimizer->optimizeStoredPhoto($absolutePath, $image->getImageName());
        $image->setImageFile($optimized);

        $this->entityManager->flush();
    }
}
