<?php

namespace Tests\Unit;

use App\Services\PhotoOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PhotoOptimizerTest extends TestCase
{
    public function test_photo_is_resized_and_optimized_when_gd_is_available(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('Ekstensi GD tidak tersedia pada PHP pengujian.');
        }

        Storage::fake('public');
        config()->set('member_photos.max_width', 800);
        config()->set('member_photos.max_height', 800);

        $path = (new PhotoOptimizer)->store(UploadedFile::fake()->image('anggota.jpg', 1800, 1200));
        [$width, $height] = getimagesize(Storage::disk('public')->path($path));

        Storage::disk('public')->assertExists($path);
        $this->assertLessThanOrEqual(800, $width);
        $this->assertLessThanOrEqual(800, $height);
        $this->assertContains(pathinfo($path, PATHINFO_EXTENSION), ['webp', 'jpg']);
    }

    public function test_clear_validation_message_is_returned_when_gd_is_missing(): void
    {
        if (extension_loaded('gd')) {
            $this->markTestSkipped('Ekstensi GD tersedia pada PHP pengujian.');
        }

        $this->expectException(ValidationException::class);
        (new PhotoOptimizer)->store(UploadedFile::fake()->create('anggota.jpg', 100, 'image/jpeg'));
    }
}
