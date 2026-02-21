<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Service\AvatarService\AvatarService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AvatarExtension extends AbstractExtension
{

    public function __construct(
            private readonly AvatarService $avatarService
    )
    {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [new TwigFunction('generate_avatar', $this->getAvatar(...))];
    }

    public function getAvatar(string $hash): string
    {
        return $this->avatarService->createAvatar($hash);
    }
}
