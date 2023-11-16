<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Image;

class ImageFactory
{
    public function create(string $dataUrl): Image
    {
        $image = new Image();
        $image->setDataUrl($dataUrl);
        return $image;
    }
}
