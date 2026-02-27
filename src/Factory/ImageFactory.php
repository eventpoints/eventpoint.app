<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFactory
{
    public function create(UploadedFile $imageFile): Image
    {
        $image = new Image();
        $image->setImageFile($imageFile);
        return $image;
    }
}
