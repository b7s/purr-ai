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
        $sourcePath = public_path('images/full-logo-PurrAI.webp');

        if (! file_exists($sourcePath)) {
            $this->error('Source icon not found: ' . $sourcePath);

            return self::FAILURE;
        }

        $image = imagecreatefromwebp($sourcePath);

        if (! $image) {
            $this->error('Failed to load WebP image');

            return self::FAILURE;
        }

        $buildDir = base_path('build');
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
            $this->info('Created build directory');
        }

        // Generate icon.png (512x512 - Electron default for Linux)
        $this->info('Generating build/icon.png (512x512)...');
        $png512 = imagescale($image, 512, 512, IMG_BICUBIC);
        imagepng($png512, $buildDir . '/icon.png', 9);
        imagedestroy($png512);

        // Generate 1024x1024 for high-res displays
        $this->info('Generating build/icon@2x.png (1024x1024)...');
        $png1024 = imagescale($image, 1024, 1024, IMG_BICUBIC);
        imagepng($png1024, $buildDir . '/icon@2x.png', 9);
        imagedestroy($png1024);

        // Generate 256x256 (common size for Windows ICO)
        $this->info('Generating build/icon-256.png...');
        $png256 = imagescale($image, 256, 256, IMG_BICUBIC);
        imagepng($png256, $buildDir . '/icon-256.png', 9);
        imagedestroy($png256);

        // Generate IconTemplate.png (16x16 for menu bar - macOS)
        $this->info('Generating public/IconTemplate.png...');
        $template16 = imagescale($image, 16, 16, IMG_BICUBIC);
        imagepng($template16, public_path('IconTemplate.png'));
        imagedestroy($template16);

        // Generate IconTemplate@2x.png (32x32 for retina - macOS)
        $this->info('Generating public/IconTemplate@2x.png...');
        $template32 = imagescale($image, 32, 32, IMG_BICUBIC);
        imagepng($template32, public_path('IconTemplate@2x.png'));
        imagedestroy($template32);

        // Copy to public for runtime use
        $this->info('Copying icon.png to public/...');
        copy($buildDir . '/icon.png', public_path('icon.png'));

        imagedestroy($image);

        $this->newLine();
        $this->info('✓ Icons generated successfully!');
        $this->newLine();
        $this->comment('Generated files:');
        $this->line('  • build/icon.png (512x512) - Linux/Electron default');
        $this->line('  • build/icon@2x.png (1024x1024) - High-res displays');
        $this->line('  • build/icon-256.png (256x256) - Windows base');
        $this->line('  • public/icon.png (512x512) - Runtime use');
        $this->line('  • public/IconTemplate.png (16x16) - macOS menu bar');
        $this->line('  • public/IconTemplate@2x.png (32x32) - macOS retina');
        $this->newLine();

        if ($this->generateWindowsIco($buildDir)) {
            $this->info('✓ Windows .ico generated');
        } else {
            $this->warn('⚠ Windows .ico requires ImageMagick or icotool');
            $this->comment('  Install: sudo apt install imagemagick');
        }

        if ($this->generateMacOsIcns($buildDir)) {
            $this->info('✓ macOS .icns generated');
        } else {
            $this->warn('⚠ macOS .icns requires png2icns or iconutil');
            $this->comment('  Install: sudo apt install icnsutils');
        }

        return self::SUCCESS;
    }

    protected function generateWindowsIco(string $buildDir): bool
    {
        if (! file_exists($buildDir . '/icon-256.png')) {
            return false;
        }

        exec('which convert', $output, $returnVar);
        if ($returnVar === 0) {
            exec("convert '{$buildDir}/icon-256.png' -define icon:auto-resize=256,128,64,48,32,16 '{$buildDir}/icon.ico'", $output, $returnVar);

            return $returnVar === 0;
        }

        exec('which icotool', $output, $returnVar);
        if ($returnVar === 0) {
            exec("icotool -c -o '{$buildDir}/icon.ico' '{$buildDir}/icon-256.png'", $output, $returnVar);

            return $returnVar === 0;
        }

        return false;
    }

    protected function generateMacOsIcns(string $buildDir): bool
    {
        if (! file_exists($buildDir . '/icon.png')) {
            return false;
        }

        exec('which png2icns', $output, $returnVar);
        if ($returnVar === 0) {
            exec("png2icns '{$buildDir}/icon.icns' '{$buildDir}/icon.png'", $output, $returnVar);

            return $returnVar === 0;
        }

        return false;
    }
}
