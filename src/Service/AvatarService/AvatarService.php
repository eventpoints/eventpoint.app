<?php

declare(strict_types=1);

namespace App\Service\AvatarService;

use Jdenticon\Identicon;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AvatarService
{
    public function createAvatar(string $hashString): string
    {
        $icon = new Identicon();

        $icon->setSize(300);
        $icon->setHash($hashString);
        $icon->setValue($hashString);

        return $icon->getImageDataUri();
    }

    public function createAvatarFile(string $hashString): UploadedFile
    {
        $icon = new Identicon();
        $icon->setSize(300);
        $icon->setHash($hashString);
        $icon->setValue($hashString);

        $dataUri = $icon->getImageDataUri();
        $commaPos = strpos($dataUri, ',');
        $meta = substr($dataUri, 5, $commaPos - 5);
        $data = substr($dataUri, $commaPos + 1);

        if (str_contains($meta, 'base64')) {
            $data = base64_decode($data);
        } else {
            $data = urldecode($data);
        }

        $ext = str_contains($meta, 'svg') ? 'svg' : 'png';
        $mime = str_contains($meta, 'svg') ? 'image/svg+xml' : 'image/png';

        $tmpPath = tempnam(sys_get_temp_dir(), 'ava_') . '.' . $ext;
        file_put_contents($tmpPath, $data);

        return new UploadedFile($tmpPath, 'avatar.' . $ext, $mime, null, true);
    }
}
