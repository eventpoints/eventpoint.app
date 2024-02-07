<?php

declare(strict_types=1);

namespace App\Model;

final readonly class Timezone
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
