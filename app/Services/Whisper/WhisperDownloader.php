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

    /**
     * @throws WhisperDownloadException
     */
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
            $os = $this->platform->getOS();
            $arch = $this->platform->getArch();

            Log::error('No FFmpeg download URL for this platform', ['os' => $os, 'arch' => $arch]);

            throw new WhisperDownloadException(
                'FFmpeg download not available for this platform',
                "OS: {$os}, Architecture: {$arch}. Please install FFmpeg manually."
            );
        }

        Log::info('Downloading FFmpeg', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.xz') ? '.tar.xz' : '.zip';
        $tempFile = $this->paths->getTempPath('ffmpeg_download_').$extension;

        $this->downloadFile($downloadUrl, $tempFile, 'FFmpeg');

        if (str_ends_with($downloadUrl, '.zip')) {
            return $this->extractFfmpegZip($tempFile);
        }

        if (str_ends_with($downloadUrl, '.tar.xz')) {
            return $this->extractFfmpegTarXz($tempFile);
        }

        @unlink($tempFile);

        return false;
    }

    /**
     * @throws WhisperDownloadException
     */
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

            $arch = $this->platform->getArch();
            Log::error('No binary download URL for this platform', ['os' => $os, 'arch' => $arch]);

            throw new WhisperDownloadException(
                'Whisper binary download not found for this platform',
                "OS: {$os}, Architecture: {$arch}"
            );
        }

        Log::info('Downloading whisper binary', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.gz') ? '.tar.gz' : '.zip';
        $tempFile = $this->paths->getTempPath('whisper_download_').$extension;

        $this->downloadFile($downloadUrl, $tempFile, 'Whisper binary');

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

    /**
     * @throws WhisperDownloadException
     */
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
            $error = trim($result->errorOutput());
            Log::error('Failed to download whisper model', ['error' => $error, 'output' => $result->output()]);
            @unlink($modelPath);

            throw new WhisperDownloadException(
                'Failed to download Whisper model',
                $error ?: 'Network error or server unavailable'
            );
        }

        if (! file_exists($modelPath) || filesize($modelPath) < 10000000) {
            $size = file_exists($modelPath) ? filesize($modelPath) : 0;
            Log::error('Downloaded model file is invalid or too small', ['path' => $modelPath, 'size' => $size]);
            @unlink($modelPath);

            throw new WhisperDownloadException(
                'Downloaded model file is invalid',
                "File size: {$size} bytes (expected > 10MB). Download may have been interrupted."
            );
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

    /**
     * @throws WhisperDownloadException
     */
    private function downloadFile(string $url, string $destination, string $component): void
    {
        $result = Process::timeout(600)->run([
            'curl', '-L', '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $destination,
            $url,
        ]);

        if (! $result->successful()) {
            $error = trim($result->errorOutput());
            Log::error("Failed to download {$component}", ['url' => $url, 'error' => $error]);
            @unlink($destination);

            throw new WhisperDownloadException(
                "Failed to download {$component}",
                $error ?: 'Network error or server unavailable'
            );
        }

        if (! file_exists($destination) || filesize($destination) < 1000) {
            $size = file_exists($destination) ? filesize($destination) : 0;
            Log::error("Downloaded {$component} file is invalid or empty", ['path' => $destination, 'size' => $size]);
            @unlink($destination);

            throw new WhisperDownloadException(
                "Downloaded {$component} file is invalid",
                "File size: {$size} bytes. Download may have been interrupted."
            );
        }
    }

    private function getFfmpegDownloadUrl(): ?string
    {
        $baseUrl = 'https://github.com/BtbN/FFmpeg-Builds/releases/download/latest';
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

    /**
     * @throws WhisperDownloadException
     */
    private function extractFfmpegZip(string $zipPath): bool
    {
        $extractDir = dirname($this->paths->getFfmpegPath());

        $result = $this->platform->isWindows()
            ? Process::run(['powershell', '-Command', "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force"])
            : Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);

        @unlink($zipPath);

        if (! $result->successful()) {
            $error = trim($result->errorOutput());
            Log::error('Failed to extract FFmpeg zip', ['error' => $error]);

            throw new WhisperDownloadException('Failed to extract FFmpeg', $error);
        }

        $this->findAndRenameFfmpeg($extractDir);

        return file_exists($this->paths->getFfmpegPath());
    }

    /**
     * @throws WhisperDownloadException
     */
    private function extractFfmpegTarXz(string $tarPath): bool
    {
        $extractDir = dirname($this->paths->getFfmpegPath());

        $result = Process::run(['tar', '-xJf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            $error = trim($result->errorOutput());
            Log::error('Failed to extract FFmpeg tar.xz', ['error' => $error]);

            throw new WhisperDownloadException('Failed to extract FFmpeg', $error);
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

    /**
     * @throws WhisperDownloadException
     */
    private function extractBinaryZip(string $zipPath): bool
    {
        $extractDir = dirname($this->paths->getBinaryPath());

        $result = $this->platform->isWindows()
            ? Process::run(['powershell', '-Command', "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force"])
            : Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);

        @unlink($zipPath);

        if (! $result->successful()) {
            $error = trim($result->errorOutput());
            Log::error('Failed to extract Whisper zip', ['error' => $error]);

            throw new WhisperDownloadException('Failed to extract Whisper binary', $error);
        }

        $this->findAndRenameBinary($extractDir);

        return file_exists($this->paths->getBinaryPath());
    }

    /**
     * @throws WhisperDownloadException
     */
    private function extractBinaryTarGz(string $tarPath): bool
    {
        $extractDir = dirname($this->paths->getBinaryPath());

        $result = Process::run(['tar', '-xzf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            $error = trim($result->errorOutput());
            Log::error('Failed to extract Whisper tar.gz', ['error' => $error]);

            throw new WhisperDownloadException('Failed to extract Whisper binary', $error);
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

    /**
     * @throws WhisperDownloadException
     */
    private function compileFromSource(): bool
    {
        $this->checkBuildDependencies();

        $tempDir = sys_get_temp_dir().'/whisper-cpp-'.uniqid();

        try {
            Log::info('Cloning whisper.cpp repository');
            $result = Process::timeout(300)->run([
                'git', 'clone', '--depth', '1',
                'https://github.com/ggerganov/whisper.cpp.git',
                $tempDir,
            ]);

            if (! $result->successful()) {
                $error = trim($result->errorOutput());
                Log::error('Failed to clone whisper.cpp', ['error' => $error]);

                throw new WhisperDownloadException(
                    'Failed to clone whisper.cpp repository',
                    $error ?: 'Git clone failed. Install git: sudo apt install git'
                );
            }

            Log::info('Compiling whisper.cpp');
            $makeResult = Process::timeout(600)->path($tempDir)->run(['make']);

            if (! $makeResult->successful()) {
                $error = trim($makeResult->errorOutput());
                Log::error('Failed to compile whisper.cpp', ['error' => $error]);

                throw new WhisperDownloadException(
                    'Failed to compile whisper.cpp',
                    $this->getBuildErrorMessage($error)
                );
            }

            $possibleBinaries = [
                'whisper-cli' => [
                    "{$tempDir}/build/bin/whisper-cli",
                    "{$tempDir}/whisper-cli",
                ],
                'main' => [
                    "{$tempDir}/build/bin/main",
                    "{$tempDir}/main",
                    "{$tempDir}/build/main",
                ],
            ];

            $binDir = dirname($this->paths->getBinaryPath());
            $libDir = "{$binDir}/../lib";

            if (! is_dir($libDir)) {
                mkdir($libDir, 0755, true);
            }

            $this->copySharedLibraries($tempDir, $libDir);

            foreach ($possibleBinaries as $targetName => $sources) {
                foreach ($sources as $sourceBinary) {
                    if (file_exists($sourceBinary)) {
                        $targetPath = "{$binDir}/{$targetName}";
                        copy($sourceBinary, $targetPath);
                        chmod($targetPath, 0755);
                        Log::info("Whisper binary '{$targetName}' compiled and installed", [
                            'source' => $sourceBinary,
                            'destination' => $targetPath,
                        ]);
                        break;
                    }
                }
            }

            $this->fixLibrarySymlinks();

            if (file_exists($this->paths->getBinaryPath())) {
                return true;
            }

            throw new WhisperDownloadException(
                'Whisper binary not found after compilation',
                'Compilation completed but binary not found in expected locations. Check logs for details.'
            );
        } finally {
            if (is_dir($tempDir)) {
                Process::run(['rm', '-rf', $tempDir]);
            }
        }
    }

    /**
     * @throws WhisperDownloadException
     */
    private function checkBuildDependencies(): void
    {
        $missing = [];

        $commands = [
            'git' => 'git --version',
            'cmake' => 'cmake --version',
            'make' => 'make --version',
        ];

        foreach ($commands as $tool => $command) {
            $result = Process::run(explode(' ', $command));
            if (! $result->successful()) {
                $missing[] = $tool;
            }
        }

        if (! empty($missing)) {
            $tools = implode(', ', $missing);
            $installCmd = $this->getInstallCommand($missing);

            throw new WhisperDownloadException(
                'Missing build dependencies',
                "Required tools not found: {$tools}. Install with: {$installCmd}"
            );
        }
    }

    /**
     * @param  array<string>  $missing
     */
    private function getInstallCommand(array $missing): string
    {
        $os = $this->platform->getOS();

        return match ($os) {
            'linux' => 'sudo apt install '.implode(' ', $missing).' build-essential',
            'darwin' => 'brew install '.implode(' ', $missing),
            default => 'Install: '.implode(', ', $missing),
        };
    }

    private function getBuildErrorMessage(string $error): string
    {
        if (str_contains($error, 'cmake: No such file')) {
            $cmd = $this->getInstallCommand(['cmake']);

            return "CMake not found. Install it with: {$cmd}";
        }

        if (str_contains($error, 'make: No such file')) {
            $cmd = $this->getInstallCommand(['make']);

            return "Make not found. Install it with: {$cmd}";
        }

        if (str_contains($error, 'gcc') || str_contains($error, 'g++')) {
            $os = $this->platform->getOS();
            $cmd = $os === 'linux' ? 'sudo apt install build-essential' : 'xcode-select --install';

            return "C++ compiler not found. Install it with: {$cmd}";
        }

        return $error ?: 'Compilation failed. Install build tools: cmake, make, gcc/clang';
    }

    private function copySharedLibraries(string $sourceDir, string $libDir): void
    {
        $libPatterns = $this->platform->isMacOS()
            ? ['libwhisper*.dylib', 'libggml*.dylib']
            : ['libwhisper.so*', 'libggml*.so*'];

        $searchDirs = [
            $sourceDir,
            "{$sourceDir}/build",
            "{$sourceDir}/build/src",
            "{$sourceDir}/build/ggml/src",
            "{$sourceDir}/src",
            "{$sourceDir}/ggml/src",
        ];

        foreach ($searchDirs as $dir) {
            if (! is_dir($dir)) {
                continue;
            }

            foreach ($libPatterns as $pattern) {
                $files = glob("{$dir}/{$pattern}");
                if ($files === false) {
                    continue;
                }

                foreach ($files as $libPath) {
                    if (is_link($libPath)) {
                        continue;
                    }

                    $targetLib = "{$libDir}/".basename($libPath);
                    if (! file_exists($targetLib)) {
                        copy($libPath, $targetLib);
                        chmod($targetLib, 0755);
                        Log::info('Whisper shared library installed', [
                            'source' => $libPath,
                            'destination' => $targetLib,
                        ]);
                    }
                }
            }
        }

        $this->createLibrarySymlinks($libDir);
    }

    private function createLibrarySymlinks(string $libDir): void
    {
        if ($this->platform->isWindows()) {
            return;
        }

        if ($this->platform->isMacOS()) {
            $this->createMacOSLibrarySymlinks($libDir);

            return;
        }

        $this->createLinuxLibrarySymlinks($libDir);
    }

    private function createLinuxLibrarySymlinks(string $libDir): void
    {
        $libs = glob("{$libDir}/*.so.*");
        if ($libs === false) {
            return;
        }

        foreach ($libs as $lib) {
            if (is_link($lib)) {
                continue;
            }

            $basename = basename($lib);

            if (preg_match('/^(.+\.so)\.(\d+)\./', $basename, $matches)) {
                $baseWithSo = $matches[1];
                $majorVersion = $matches[2];

                $versionedSymlink = "{$libDir}/{$baseWithSo}.{$majorVersion}";
                if (! file_exists($versionedSymlink)) {
                    @symlink($basename, $versionedSymlink);
                    Log::info('Created library symlink', [
                        'symlink' => $versionedSymlink,
                        'target' => $basename,
                    ]);
                }

                $baseSymlink = "{$libDir}/{$baseWithSo}";
                if (! file_exists($baseSymlink)) {
                    @symlink($basename, $baseSymlink);
                }
            }
        }
    }

    private function createMacOSLibrarySymlinks(string $libDir): void
    {
        $libs = glob("{$libDir}/*.dylib");
        if ($libs === false) {
            return;
        }

        foreach ($libs as $lib) {
            if (is_link($lib)) {
                continue;
            }

            $basename = basename($lib);

            if (preg_match('/^(.+)\.(\d+)\.dylib$/', $basename, $matches)) {
                $baseName = $matches[1];
                $majorVersion = $matches[2];

                $versionedSymlink = "{$libDir}/{$baseName}.{$majorVersion}.dylib";
                if (! file_exists($versionedSymlink) && $versionedSymlink !== $lib) {
                    @symlink($basename, $versionedSymlink);
                }

                $baseSymlink = "{$libDir}/{$baseName}.dylib";
                if (! file_exists($baseSymlink)) {
                    @symlink($basename, $baseSymlink);
                    Log::info('Created library symlink', [
                        'symlink' => $baseSymlink,
                        'target' => $basename,
                    ]);
                }
            }
        }
    }

    public function fixLibrarySymlinks(): void
    {
        $binDir = dirname($this->paths->getBinaryPath());
        $libDir = "{$binDir}/../lib";

        if (! is_dir($libDir)) {
            return;
        }

        $this->createLibrarySymlinks($libDir);
    }

    /**
     * @throws WhisperDownloadException
     */
    public function reinstallBinary(): bool
    {
        $binaryPath = $this->paths->getBinaryPath();
        $binDir = dirname($binaryPath);
        $libDir = "{$binDir}/../lib";

        foreach (glob("{$binDir}/whisper*") ?: [] as $file) {
            @unlink($file);
        }
        foreach (glob("{$binDir}/main") ?: [] as $file) {
            @unlink($file);
        }

        if (is_dir($libDir)) {
            foreach (glob("{$libDir}/*.so*") ?: [] as $file) {
                @unlink($file);
            }
        }

        return $this->downloadBinary();
    }
}
