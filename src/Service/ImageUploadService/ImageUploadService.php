<?php

declare(strict_types=1);

namespace App\Service\ImageUploadService;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploadService
{
    public function processAvatar(UploadedFile $file): Image
    {
        $manager = new ImageManager([
            'driver' => 'imagick',
        ]);
        $image = $manager->make($file->getRealPath());
        $image->fit(400, 400);
        return $image->encode('data-url');
    }

    public function processPhoto(UploadedFile $file): Image
    {
        $manager = new ImageManager([
            'driver' => 'imagick',
        ]);
        $image = $manager->make($file->getRealPath());

        $image->resize(800, 800, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image->encode('data-url');
    }
}
