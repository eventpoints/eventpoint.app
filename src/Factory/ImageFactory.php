<?php

namespace App\Factory;

use App\Entity\Event\Event;
use App\Entity\Image;
use App\Entity\User;

class ImageFactory
{
    public function create(string $dataUrl) : Image
    {
        $image = new Image();
        $image->setDataUrl($dataUrl);
        return $image;
    }

}