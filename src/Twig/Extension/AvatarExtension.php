<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\User\User;
use App\Service\AvatarService\AvatarService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class AvatarExtension extends AbstractExtension
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly UploaderHelper $uploaderHelper,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_avatar', $this->getAvatar(...)),
            new TwigFunction('user_avatar', $this->getUserAvatar(...)),
        ];
    }

    public function getAvatar(string $hash): string
    {
        return $this->avatarService->createAvatar($hash);
    }

    public function getUserAvatar(User $user): string
    {
        if ($user->getAvatarName() !== null) {
            return $this->uploaderHelper->asset($user, 'avatarFile') ?? '';
        }

        if ($user->getAvatarUrl() !== null) {
            return $user->getAvatarUrl();
        }

        return $this->avatarService->createAvatar($user->getEmail()?->getAddress() ?? (string) $user->getId());
    }
}
