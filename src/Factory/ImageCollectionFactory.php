<?php

namespace App\Factory;

use App\Entity\Event\Event;
use App\Entity\Image;
use App\Entity\ImageCollection;
use App\Entity\User;

class ImageCollectionFactory
{
    /**
     * @param array<int, Image> $images
     * @param User $owner
     * @return ImageCollection
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