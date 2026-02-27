<?php

declare(strict_types=1);

namespace App\Service\ImageUploadService;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploadService
{
    public function __construct(
        private ImageOptimizer $optimizer
    ) {
    }

    public function processAvatar(UploadedFile $file): UploadedFile
    {
        return $this->optimizer->optimizeAvatar($file);
    }

    public function processPhoto(UploadedFile $file): UploadedFile
    {
        return $this->optimizer->optimizePhoto($file);
    }
}
