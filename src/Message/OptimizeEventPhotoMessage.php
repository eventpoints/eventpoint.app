<?php

declare(strict_types=1);

namespace App\Message;

final readonly class OptimizeEventPhotoMessage
{
    public function __construct(
        public string $imageId
    ) {
    }
}
