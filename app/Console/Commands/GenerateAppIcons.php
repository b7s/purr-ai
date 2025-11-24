<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAppIcons extends Command
{
    protected $signature = 'app:generate-icons';

    protected $description = 'Generate application icons for NativePHP from WebP source';

    public function handle(): int
    {
        $sourcePath = public_path('images/logo-PurrAI-256.webp');

        if (! file_exists($sourcePath)) {
            $this->error('Source icon not found: '.$sourcePath);

            return self::FAILURE;
        }

        // Load WebP image
        $image = imagecreatefromwebp($sourcePath);

        if (! $image) {
            $this->error('Failed to load WebP image');

            return self::FAILURE;
        }

        // Generate icon.png (1024x1024 for best quality on Linux)
        $this->info('Generating icon.png (1024x1024)...');
        $png1024 = imagescale($image, 1024, 1024, IMG_BICUBIC);
        imagepng($png1024, public_path('icon.png'), 9);
        imagedestroy($png1024);

        // Generate IconTemplate.png (16x16 for menu bar - macOS)
        $this->info('Generating IconTemplate.png...');
        $template16 = imagescale($image, 16, 16, IMG_BICUBIC);
        imagepng($template16, public_path('IconTemplate.png'));
        imagedestroy($template16);

        // Generate IconTemplate@2x.png (32x32 for retina - macOS)
        $this->info('Generating IconTemplate@2x.png...');
        $template32 = imagescale($image, 32, 32, IMG_BICUBIC);
        imagepng($template32, public_path('IconTemplate@2x.png'));
        imagedestroy($template32);

        imagedestroy($image);

        $this->info('Icons generated successfully!');
        $this->warn('Note: .ico (Windows) and .icns (macOS) files need external tools.');
        $this->warn('For production builds, use proper icon conversion tools.');

        return self::SUCCESS;
    }
}
