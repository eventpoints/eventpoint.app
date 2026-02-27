<?php

declare(strict_types=1);

namespace App\Service\ImageUploadService;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageOptimizer
{
    private ImageManager $images;

    public function __construct()
    {
        $this->images = new ImageManager(new Driver());
    }

    public function optimizeAvatar(
        UploadedFile $file,
        int $targetBytes = 120_000,
        int $maxDim = 512,
        int $minDim = 128,
    ): UploadedFile {
        $sourcePath = $file->getPathname();
        $driver = $this->images->driver();

        $supportsAvif = $driver->supports('avif');
        $supportsWebp = $driver->supports('webp');

        $quality = $supportsAvif ? 45 : ($supportsWebp ? 70 : 80);
        $minQ = $supportsAvif ? 28 : ($supportsWebp ? 50 : 60);
        $ext = $supportsAvif ? 'avif' : ($supportsWebp ? 'webp' : 'jpg');

        $tmpBase = tempnam(sys_get_temp_dir(), 'ava_');
        @unlink($tmpBase);
        $dest = $tmpBase . '.' . $ext;

        while (true) {
            $img = $this->images->read($sourcePath);

            $w = $img->width();
            $h = $img->height();
            $side = min($w, $h);
            $img->crop($side, $side, (int) floor(($w - $side) / 2), (int) floor(($h - $side) / 2));
            $img->scaleDown(width: $maxDim, height: $maxDim);

            $encoded = match ($ext) {
                'avif' => $img->toAvif(quality: $quality, strip: true),
                'webp' => $img->toWebp(quality: $quality, strip: true),
                default => $img->toJpeg(quality: $quality, progressive: true, strip: true),
            };

            $encoded->save($dest);
            $size = filesize($dest) ?: PHP_INT_MAX;

            if ($size <= $targetBytes) {
                break;
            }

            if ($quality > $minQ) {
                $quality -= 5;
            } elseif ($maxDim > $minDim) {
                $maxDim = max((int) floor($maxDim * 0.9), $minDim);
            } else {
                break;
            }
        }

        $mime = $ext === 'jpg' ? 'image/jpeg' : "image/{$ext}";

        return new UploadedFile($dest, 'avatar.' . $ext, $mime, null, true);
    }

    public function optimizePhoto(
        UploadedFile $file,
        int $targetBytes = 2_000_000,
        int $maxDim = 1920,
        int $minDim = 640,
    ): UploadedFile {
        $sourcePath = $file->getPathname();
        $driver = $this->images->driver();

        $supportsWebp = $driver->supports('webp');

        $quality = $supportsWebp ? 70 : 75;
        $minQ = $supportsWebp ? 50 : 55;
        $ext = $supportsWebp ? 'webp' : 'jpg';

        $tmpBase = tempnam(sys_get_temp_dir(), 'img_');
        @unlink($tmpBase);
        $dest = $tmpBase . '.' . $ext;

        while (true) {
            $img = $this->images->read($sourcePath)->scaleDown(width: $maxDim, height: $maxDim);

            $encoded = $ext === 'webp'
                ? $img->toWebp(quality: $quality, strip: true)
                : $img->toJpeg(quality: $quality, progressive: true, strip: true);

            $encoded->save($dest);
            $size = filesize($dest) ?: PHP_INT_MAX;

            if ($size <= $targetBytes) {
                break;
            }

            if ($quality > $minQ) {
                $quality -= 5;
            } elseif ($maxDim > $minDim) {
                $maxDim = max((int) floor($maxDim * 0.9), $minDim);
            } else {
                break;
            }
        }

        $mime = $ext === 'jpg' ? 'image/jpeg' : "image/{$ext}";

        return new UploadedFile($dest, 'photo.' . $ext, $mime, null, true);
    }

    public function optimizeStoredPhoto(string $absolutePath, string $originalName): UploadedFile
    {
        $fakeUploaded = new UploadedFile($absolutePath, $originalName, null, null, true);

        return $this->optimizePhoto($fakeUploaded);
    }
}
