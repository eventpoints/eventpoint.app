<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Event\Event;
use App\Entity\Image\Image;
use App\Entity\Image\ImageCollection;
use App\Entity\User\User;

class ImageCollectionFactory
{
    /**
     * @param array<int, Image> $images
     */
    public function create(array $images, User $owner, Event $event): ImageCollection
    {
        $imageCollection = new ImageCollection();
        foreach ($images as $image) {
            $imageCollection->addImage($image);
        }
        $imageCollection->setOwner($owner);
        $imageCollection->setEvent($event);
        return $imageCollection;
    }
}
