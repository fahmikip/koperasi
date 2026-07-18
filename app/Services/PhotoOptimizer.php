<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class PhotoOptimizer
{
    public function store(UploadedFile $photo): string
    {
        $this->ensureGdIsAvailable();

        $disk = Storage::disk(config('member_photos.disk', 'public'));
        $directory = trim(config('member_photos.directory', 'members'), '/');
        $extension = $this->outputExtension();
        $path = $directory.'/'.Str::uuid().'.'.$extension;
        $disk->makeDirectory($directory);

        try {
            $manager = new ImageManager(new Driver, autoOrientation: true, strip: true);
            $manager->decodePath($photo->getRealPath())
                ->orient()
                ->scaleDown(
                    width: (int) config('member_photos.max_width', 1200),
                    height: (int) config('member_photos.max_height', 1200),
                )
                ->save($disk->path($path), quality: (int) config('member_photos.quality', 82));
        } catch (Throwable $exception) {
            $disk->delete($path);
            report($exception);

            throw ValidationException::withMessages([
                'photo' => 'Foto gagal diproses. Pastikan file gambar valid dan ekstensi GD aktif.',
            ]);
        }

        return $path;
    }

    private function ensureGdIsAvailable(): void
    {
        if (! extension_loaded('gd') || ! function_exists('gd_info')) {
            throw ValidationException::withMessages([
                'photo' => 'Server belum mengaktifkan ekstensi GD. Aktifkan extension=gd pada php.ini lalu restart Apache/PHP.',
            ]);
        }
    }

    private function outputExtension(): string
    {
        $format = strtolower((string) config('member_photos.format', 'webp'));

        if ($format === 'webp' && (gd_info()['WebP Support'] ?? false)) {
            return 'webp';
        }

        return 'jpg';
    }
}
