<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class WhisperDownloader
{
    public function __construct(
        private readonly WhisperPlatformDetector $platform,
        private readonly WhisperPathResolver $paths,
    ) {}

    public function downloadFfmpeg(): bool
    {
        $ffmpegPath = $this->paths->getFfmpegPath();

        if ($this->isFfmpegAvailable()) {
            return true;
        }

        if (file_exists($ffmpegPath)) {
            return true;
        }

        $this->paths->ensureDirectoriesExist();

        $downloadUrl = $this->getFfmpegDownloadUrl();

        if (! $downloadUrl) {
            Log::error('No FFmpeg download URL for this platform', [
                'os' => $this->platform->getOS(),
                'arch' => $this->platform->getArch(),
            ]);

            return false;
        }

        Log::info('Downloading FFmpeg', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.xz') ? '.tar.xz' : '.zip';
        $tempFile = $this->paths->getTempPath('ffmpeg_download_').$extension;

        if (! $this->downloadFile($downloadUrl, $tempFile)) {
            return false;
        }

        if (str_ends_with($downloadUrl, '.zip')) {
            return $this->extractFfmpegZip($tempFile);
        }

        if (str_ends_with($downloadUrl, '.tar.xz')) {
            return $this->extractFfmpegTarXz($tempFile);
        }

        @unlink($tempFile);

        return false;
    }

    public function downloadBinary(): bool
    {
        $binaryPath = $this->paths->getBinaryPath();

        if (file_exists($binaryPath)) {
            return true;
        }

        $this->paths->ensureDirectoriesExist();

        $downloadUrl = $this->getBinaryDownloadUrl();

        if (! $downloadUrl) {
            $os = $this->platform->getOS();
            if ($os === 'linux' || $os === 'darwin') {
                return $this->compileFromSource();
            }

            Log::error('No binary download URL for this platform', [
                'os' => $os,
                'arch' => $this->platform->getArch(),
            ]);

            return false;
        }

        Log::info('Downloading whisper binary', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.gz') ? '.tar.gz' : '.zip';
        $tempFile = $this->paths->getTempPath('whisper_download_').$extension;

        if (! $this->downloadFile($downloadUrl, $tempFile)) {
            return false;
        }

        if (str_ends_with($downloadUrl, '.zip')) {
            return $this->extractBinaryZip($tempFile);
        }

        if (str_ends_with($downloadUrl, '.tar.gz')) {
            return $this->extractBinaryTarGz($tempFile);
        }

        rename($tempFile, $binaryPath);
        chmod($binaryPath, 0755);

        return true;
    }

    public function downloadModel(string $model = 'base.en'): bool
    {
        $modelPath = $this->paths->getModelPath();

        if (file_exists($modelPath)) {
            return true;
        }

        $this->paths->ensureDirectoriesExist();

        $modelUrl = "https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-{$model}.bin";

        Log::info('Downloading whisper model', ['url' => $modelUrl]);

        $result = Process::timeout(600)->run([
            'curl', '-L', '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $modelPath,
            $modelUrl,
        ]);

        if (! $result->successful()) {
            Log::error('Failed to download whisper model', [
                'error' => $result->errorOutput(),
                'output' => $result->output(),
            ]);
            @unlink($modelPath);

            return false;
        }

        if (! file_exists($modelPath) || filesize($modelPath) < 10000000) {
            Log::error('Downloaded model file is invalid or too small', [
                'path' => $modelPath,
                'size' => file_exists($modelPath) ? filesize($modelPath) : 0,
            ]);
            @unlink($modelPath);

            return false;
        }

        return true;
    }

    public function isFfmpegAvailable(): bool
    {
        $ffmpegPath = $this->paths->getFfmpegPath();

        if (file_exists($ffmpegPath)) {
            return true;
        }

        $which = $this->platform->isWindows() ? 'where' : 'which';
        $result = Process::run([$which, 'ffmpeg']);

        return $result->successful();
    }

    private function downloadFile(string $url, string $destination): bool
    {
        $result = Process::timeout(600)->run([
            'curl', '-L', '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $destination,
            $url,
        ]);

        if (! $result->successful()) {
            Log::error('Failed to download file', [
                'url' => $url,
                'error' => $result->errorOutput(),
            ]);
            @unlink($destination);

            return false;
        }

        if (! file_exists($destination) || filesize($destination) < 1000) {
            Log::error('Downloaded file is invalid or empty', [
                'path' => $destination,
                'size' => file_exists($destination) ? filesize($destination) : 0,
            ]);
            @unlink($destination);

            return false;
        }

        return true;
    }

    private function getFfmpegDownloadUrl(): ?string
    {
        $baseUrl = 'https://github.com/BtbN/FFmpeg-Builds/releases/tag/latest';
        $os = $this->platform->getOS();
        $arch = $this->platform->getArch();

        return match ($os) {
            'windows' => match ($arch) {
                'x86_64', 'amd64' => "{$baseUrl}/ffmpeg-master-latest-win64-gpl.zip",
                default => null,
            },
            'linux' => match ($arch) {
                'x86_64', 'amd64' => "{$baseUrl}/ffmpeg-master-latest-linux64-gpl.tar.xz",
                'arm64', 'aarch64' => "{$baseUrl}/ffmpeg-master-latest-linuxarm64-gpl.tar.xz",
                default => null,
            },
            default => null,
        };
    }

    private function getBinaryDownloadUrl(): ?string
    {
        $baseUrl = 'https://github.com/ggerganov/whisper.cpp/releases/latest/download';
        $os = $this->platform->getOS();
        $arch = $this->platform->getArch();

        return match ($os) {
            'windows' => match ($arch) {
                'x86_64', 'amd64' => "{$baseUrl}/whisper-bin-x64.zip",
                default => "{$baseUrl}/whisper-bin-Win32.zip",
            },
            default => null,
        };
    }

    private function extractFfmpegZip(string $zipPath): bool
    {
        $extractDir = dirname($this->paths->getFfmpegPath());

        $result = $this->platform->isWindows()
            ? Process::run(['powershell', '-Command', "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force"])
            : Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);

        @unlink($zipPath);

        if (! $result->successful()) {
            Log::error('Failed to extract FFmpeg zip', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameFfmpeg($extractDir);

        return file_exists($this->paths->getFfmpegPath());
    }

    private function extractFfmpegTarXz(string $tarPath): bool
    {
        $extractDir = dirname($this->paths->getFfmpegPath());

        $result = Process::run(['tar', '-xJf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            Log::error('Failed to extract FFmpeg tar.xz', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameFfmpeg($extractDir);

        return file_exists($this->paths->getFfmpegPath());
    }

    private function findAndRenameFfmpeg(string $dir): void
    {
        $ffmpegPath = $this->paths->getFfmpegPath();
        $possibleNames = ['ffmpeg', 'ffmpeg.exe'];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (\in_array($file->getFilename(), $possibleNames, true)) {
                if ($file->getPathname() !== $ffmpegPath) {
                    rename($file->getPathname(), $ffmpegPath);
                    chmod($ffmpegPath, 0755);
                }

                return;
            }
        }
    }

    private function extractBinaryZip(string $zipPath): bool
    {
        $extractDir = dirname($this->paths->getBinaryPath());

        $result = $this->platform->isWindows()
            ? Process::run(['powershell', '-Command', "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force"])
            : Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);

        @unlink($zipPath);

        if (! $result->successful()) {
            Log::error('Failed to extract zip', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameBinary($extractDir);

        return file_exists($this->paths->getBinaryPath());
    }

    private function extractBinaryTarGz(string $tarPath): bool
    {
        $extractDir = dirname($this->paths->getBinaryPath());

        $result = Process::run(['tar', '-xzf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            Log::error('Failed to extract tar.gz', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameBinary($extractDir);

        return file_exists($this->paths->getBinaryPath());
    }

    private function findAndRenameBinary(string $dir): void
    {
        $binaryPath = $this->paths->getBinaryPath();
        $possibleNames = ['whisper', 'whisper.exe', 'main', 'main.exe', 'whisper-cli', 'whisper-cli.exe'];

        foreach ($possibleNames as $name) {
            $path = "{$dir}/{$name}";
            if (file_exists($path) && $path !== $binaryPath) {
                rename($path, $binaryPath);
                chmod($binaryPath, 0755);

                return;
            }
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (\in_array($file->getFilename(), $possibleNames, true)) {
                rename($file->getPathname(), $binaryPath);
                chmod($binaryPath, 0755);

                return;
            }
        }
    }

    private function compileFromSource(): bool
    {
        $tempDir = sys_get_temp_dir().'/whisper-cpp-'.uniqid();

        try {
            Log::info('Cloning whisper.cpp repository');
            $result = Process::timeout(300)->run([
                'git', 'clone', '--depth', '1',
                'https://github.com/ggerganov/whisper.cpp.git',
                $tempDir,
            ]);

            if (! $result->successful()) {
                Log::error('Failed to clone whisper.cpp', ['error' => $result->errorOutput()]);

                return false;
            }

            Log::info('Compiling whisper.cpp');
            $makeResult = Process::timeout(600)->path($tempDir)->run(['make']);

            if (! $makeResult->successful()) {
                Log::error('Failed to compile whisper.cpp', ['error' => $makeResult->errorOutput()]);

                return false;
            }

            $sourceBinary = "{$tempDir}/main";
            if (file_exists($sourceBinary)) {
                $binaryPath = $this->paths->getBinaryPath();
                copy($sourceBinary, $binaryPath);
                chmod($binaryPath, 0755);

                return true;
            }

            return false;
        } finally {
            if (is_dir($tempDir)) {
                Process::run(['rm', '-rf', $tempDir]);
            }
        }
    }
}
