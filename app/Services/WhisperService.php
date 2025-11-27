<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

final class WhisperService
{
    private string $modelPath;

    private string $binaryPath;

    private string $dataDir;

    public function __construct()
    {
        $this->dataDir = $this->getDataDirectory();
        $this->binaryPath = $this->getBinaryPath();
        $this->modelPath = $this->getModelPath();
    }

    public function transcribe(UploadedFile $audioFile): string
    {
        if (! $this->isAvailable()) {
            Log::warning('Whisper not available, returning empty transcription');

            return '';
        }

        $tempWavPath = $this->convertToWav($audioFile);

        try {
            $result = $this->runWhisper($tempWavPath);
        } finally {
            @unlink($tempWavPath);
        }

        return $result;
    }

    public function transcribeFromPath(string $audioPath): string
    {
        if (! $this->isAvailable()) {
            return '';
        }

        $tempWavPath = $this->convertFileToWav($audioPath);

        try {
            $result = $this->runWhisper($tempWavPath);
        } finally {
            @unlink($tempWavPath);
        }

        return $result;
    }

    private function convertToWav(UploadedFile $audioFile): string
    {
        $tempInputPath = $this->getTempPath('audio_input_').'.webm';
        $audioFile->move(dirname($tempInputPath), basename($tempInputPath));

        return $this->convertFileToWav($tempInputPath);
    }

    private function convertFileToWav(string $inputPath): string
    {
        $tempWavPath = $this->getTempPath('audio_wav_').'.wav';
        $ffmpegPath = $this->getFfmpegPath();

        $result = Process::run([
            $ffmpegPath,
            '-i', $inputPath,
            '-ar', '16000',
            '-ac', '1',
            '-c:a', 'pcm_s16le',
            '-y',
            $tempWavPath,
        ]);

        @unlink($inputPath);

        if (! $result->successful()) {
            Log::error('FFmpeg conversion failed', [
                'error' => $result->errorOutput(),
            ]);
            throw new RuntimeException('Failed to convert audio file');
        }

        return $tempWavPath;
    }

    private function runWhisper(string $wavPath): string
    {
        $args = [
            $this->binaryPath,
            '-m', $this->modelPath,
            '-f', $wavPath,
            '-l', 'en',
            '-nt',
            '--no-timestamps',
        ];

        // Try GPU first, fallback to CPU
        if ($this->hasGpuSupport()) {
            $args[] = '-ng'; // Use GPU
        }

        $result = Process::timeout(120)->run($args);

        // If GPU failed, retry with CPU
        if (! $result->successful() && $this->hasGpuSupport()) {
            Log::info('GPU transcription failed, falling back to CPU');
            $args = array_filter($args, fn ($arg) => $arg !== '-ng');
            $result = Process::timeout(120)->run($args);
        }

        if (! $result->successful()) {
            Log::error('Whisper transcription failed', [
                'error' => $result->errorOutput(),
            ]);

            return '';
        }

        return trim($result->output());
    }

    public function isAvailable(): bool
    {
        return file_exists($this->binaryPath) && file_exists($this->modelPath);
    }

    public function hasGpuSupport(): bool
    {
        return match ($this->getOS()) {
            'darwin' => $this->hasMacGpu(),
            'windows' => $this->hasWindowsGpu(),
            'linux' => $this->hasLinuxGpu(),
            default => false,
        };
    }

    private function hasMacGpu(): bool
    {
        // macOS with Apple Silicon has Metal support
        $result = Process::run(['sysctl', '-n', 'machdep.cpu.brand_string']);

        return $result->successful() && str_contains(strtolower($result->output()), 'apple');
    }

    private function hasWindowsGpu(): bool
    {
        // Check for NVIDIA GPU on Windows
        $result = Process::run(['where', 'nvidia-smi']);

        if ($result->successful()) {
            $nvidiaCheck = Process::run(['nvidia-smi', '-L']);

            return $nvidiaCheck->successful();
        }

        return false;
    }

    private function hasLinuxGpu(): bool
    {
        // Check for NVIDIA GPU on Linux
        $result = Process::run(['which', 'nvidia-smi']);

        if ($result->successful()) {
            $nvidiaCheck = Process::run(['nvidia-smi', '-L']);

            return $nvidiaCheck->successful();
        }

        return false;
    }

    /**
     * @return array{binary: bool, model: bool, ffmpeg: bool}
     */
    public function getStatus(): array
    {
        return [
            'binary' => file_exists($this->binaryPath),
            'model' => file_exists($this->modelPath),
            'ffmpeg' => $this->isFfmpegAvailable(),
            'gpu' => $this->hasGpuSupport(),
        ];
    }

    public function setup(): bool
    {
        $this->ensureDirectoriesExist();

        $ffmpegDownloaded = $this->downloadFfmpeg();
        $binaryDownloaded = $this->downloadBinary();
        $modelDownloaded = $this->downloadModel();

        return $ffmpegDownloaded && $binaryDownloaded && $modelDownloaded;
    }

    private function ensureDirectoriesExist(): void
    {
        $dirs = [
            $this->dataDir,
            dirname($this->binaryPath),
            dirname($this->modelPath),
            dirname($this->getFfmpegPath()),
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function downloadFfmpeg(): bool
    {
        $ffmpegPath = $this->getFfmpegPath();

        // Check if already exists in system PATH
        if ($this->isFfmpegAvailable()) {
            return true;
        }

        // Check if already downloaded
        if (file_exists($ffmpegPath)) {
            return true;
        }

        $this->ensureDirectoriesExist();

        $os = $this->getOS();
        $arch = $this->getArch();

        $downloadUrl = $this->getFfmpegDownloadUrl($os, $arch);

        if (! $downloadUrl) {
            Log::error('No FFmpeg download URL for this platform', [
                'os' => $os,
                'arch' => $arch,
            ]);

            return false;
        }

        Log::info('Downloading FFmpeg', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.xz') ? '.tar.xz' : '.zip';
        $tempFile = $this->getTempPath('ffmpeg_download_').$extension;

        $result = Process::timeout(600)->run([
            'curl',
            '-L',
            '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $tempFile,
            $downloadUrl,
        ]);

        if (! $result->successful()) {
            Log::error('Failed to download FFmpeg', [
                'error' => $result->errorOutput(),
                'output' => $result->output(),
            ]);
            @unlink($tempFile);

            return false;
        }

        // Validate file was downloaded and has content
        if (! file_exists($tempFile) || filesize($tempFile) < 1000) {
            Log::error('Downloaded FFmpeg file is invalid or empty', [
                'path' => $tempFile,
                'size' => file_exists($tempFile) ? filesize($tempFile) : 0,
            ]);
            @unlink($tempFile);

            return false;
        }

        // Extract based on file type
        if (str_ends_with($downloadUrl, '.zip')) {
            return $this->extractFfmpegZip($tempFile);
        } elseif (str_ends_with($downloadUrl, '.tar.xz')) {
            return $this->extractFfmpegTarXz($tempFile);
        }

        @unlink($tempFile);

        return false;
    }

    private function getFfmpegDownloadUrl(string $os, string $arch): ?string
    {
        // BtbN/FFmpeg-Builds uses "latest" as the actual tag name
        $baseUrl = 'https://github.com/BtbN/FFmpeg-Builds/releases/tag/latest';

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
            'darwin' => null, // macOS users should use homebrew
            default => null,
        };
    }

    private function extractFfmpegZip(string $zipPath): bool
    {
        $extractDir = dirname($this->getFfmpegPath());

        if ($this->getOS() === 'windows') {
            $result = Process::run([
                'powershell',
                '-Command',
                "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force",
            ]);
        } else {
            $result = Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);
        }

        @unlink($zipPath);

        if (! $result->successful()) {
            Log::error('Failed to extract FFmpeg zip', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameFfmpeg($extractDir);

        return file_exists($this->getFfmpegPath());
    }

    private function extractFfmpegTarXz(string $tarPath): bool
    {
        $extractDir = dirname($this->getFfmpegPath());

        $result = Process::run(['tar', '-xJf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            Log::error('Failed to extract FFmpeg tar.xz', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameFfmpeg($extractDir);

        return file_exists($this->getFfmpegPath());
    }

    private function findAndRenameFfmpeg(string $dir): void
    {
        $ffmpegPath = $this->getFfmpegPath();
        $possibleNames = ['ffmpeg', 'ffmpeg.exe'];

        // Search recursively for ffmpeg binary
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (in_array($file->getFilename(), $possibleNames, true)) {
                // Found ffmpeg, move to correct location
                if ($file->getPathname() !== $ffmpegPath) {
                    rename($file->getPathname(), $ffmpegPath);
                    chmod($ffmpegPath, 0755);
                }

                return;
            }
        }
    }

    public function downloadBinary(): bool
    {
        if (file_exists($this->binaryPath)) {
            return true;
        }

        $this->ensureDirectoriesExist();

        $os = $this->getOS();
        $arch = $this->getArch();

        $downloadUrl = $this->getBinaryDownloadUrl($os, $arch);

        // For Linux/macOS, compile from source
        if (! $downloadUrl) {
            if ($os === 'linux' || $os === 'darwin') {
                return $this->compileFromSource();
            }

            Log::error('No binary download URL for this platform', [
                'os' => $os,
                'arch' => $arch,
            ]);

            return false;
        }

        Log::info('Downloading whisper binary', ['url' => $downloadUrl]);

        $extension = str_ends_with($downloadUrl, '.tar.gz') ? '.tar.gz' : '.zip';
        $tempFile = $this->getTempPath('whisper_download_').$extension;

        $result = Process::timeout(300)->run([
            'curl',
            '-L',
            '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $tempFile,
            $downloadUrl,
        ]);

        if (! $result->successful()) {
            Log::error('Failed to download whisper binary', [
                'error' => $result->errorOutput(),
                'output' => $result->output(),
            ]);
            @unlink($tempFile);

            return false;
        }

        // Validate file was downloaded and has content
        if (! file_exists($tempFile) || filesize($tempFile) < 1000) {
            Log::error('Downloaded whisper binary file is invalid or empty', [
                'path' => $tempFile,
                'size' => file_exists($tempFile) ? filesize($tempFile) : 0,
            ]);
            @unlink($tempFile);

            return false;
        }

        // Extract if it's a zip/tar file
        if (str_ends_with($downloadUrl, '.zip')) {
            return $this->extractZip($tempFile);
        } elseif (str_ends_with($downloadUrl, '.tar.gz')) {
            return $this->extractTarGz($tempFile);
        } else {
            // Direct binary
            rename($tempFile, $this->binaryPath);
            chmod($this->binaryPath, 0755);

            return true;
        }
    }

    public function downloadModel(string $model = 'base.en'): bool
    {
        if (file_exists($this->modelPath)) {
            return true;
        }

        $this->ensureDirectoriesExist();

        $modelUrl = "https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-{$model}.bin";

        Log::info('Downloading whisper model', ['url' => $modelUrl]);

        $result = Process::timeout(600)->run([
            'curl',
            '-L',
            '-f',
            '--retry', '3',
            '--retry-delay', '2',
            '-o', $this->modelPath,
            $modelUrl,
        ]);

        if (! $result->successful()) {
            Log::error('Failed to download whisper model', [
                'error' => $result->errorOutput(),
                'output' => $result->output(),
            ]);
            @unlink($this->modelPath);

            return false;
        }

        // Validate file was downloaded and has content (model should be > 10MB)
        if (! file_exists($this->modelPath) || filesize($this->modelPath) < 10000000) {
            Log::error('Downloaded model file is invalid or too small', [
                'path' => $this->modelPath,
                'size' => file_exists($this->modelPath) ? filesize($this->modelPath) : 0,
            ]);
            @unlink($this->modelPath);

            return false;
        }

        return true;
    }

    private function extractZip(string $zipPath): bool
    {
        $extractDir = dirname($this->binaryPath);

        if ($this->getOS() === 'windows') {
            $result = Process::run([
                'powershell',
                '-Command',
                "Expand-Archive -Path '{$zipPath}' -DestinationPath '{$extractDir}' -Force",
            ]);
        } else {
            $result = Process::run(['unzip', '-o', $zipPath, '-d', $extractDir]);
        }

        @unlink($zipPath);

        if (! $result->successful()) {
            Log::error('Failed to extract zip', ['error' => $result->errorOutput()]);

            return false;
        }

        // Find and rename the binary
        $this->findAndRenameBinary($extractDir);

        return file_exists($this->binaryPath);
    }

    private function extractTarGz(string $tarPath): bool
    {
        $extractDir = dirname($this->binaryPath);

        $result = Process::run(['tar', '-xzf', $tarPath, '-C', $extractDir]);

        @unlink($tarPath);

        if (! $result->successful()) {
            Log::error('Failed to extract tar.gz', ['error' => $result->errorOutput()]);

            return false;
        }

        $this->findAndRenameBinary($extractDir);

        return file_exists($this->binaryPath);
    }

    private function findAndRenameBinary(string $dir): void
    {
        $possibleNames = ['whisper', 'whisper.exe', 'main', 'main.exe', 'whisper-cli', 'whisper-cli.exe'];

        foreach ($possibleNames as $name) {
            $path = $dir.'/'.$name;
            if (file_exists($path) && $path !== $this->binaryPath) {
                rename($path, $this->binaryPath);
                chmod($this->binaryPath, 0755);

                return;
            }
        }

        // Search recursively
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (in_array($file->getFilename(), $possibleNames, true)) {
                rename($file->getPathname(), $this->binaryPath);
                chmod($this->binaryPath, 0755);

                return;
            }
        }
    }

    private function compileFromSource(): bool
    {
        $tempDir = sys_get_temp_dir().'/whisper-cpp-'.uniqid();

        try {
            // Clone repository
            Log::info('Cloning whisper.cpp repository');
            $result = Process::timeout(300)->run([
                'git',
                'clone',
                '--depth', '1',
                'https://github.com/ggerganov/whisper.cpp.git',
                $tempDir,
            ]);

            if (! $result->successful()) {
                Log::error('Failed to clone whisper.cpp', ['error' => $result->errorOutput()]);

                return false;
            }

            // Compile
            Log::info('Compiling whisper.cpp');
            $makeResult = Process::timeout(600)->path($tempDir)->run(['make']);

            if (! $makeResult->successful()) {
                Log::error('Failed to compile whisper.cpp', ['error' => $makeResult->errorOutput()]);

                return false;
            }

            // Copy binary
            $sourceBinary = $tempDir.'/main';
            if (file_exists($sourceBinary)) {
                copy($sourceBinary, $this->binaryPath);
                chmod($this->binaryPath, 0755);

                return true;
            }

            return false;
        } finally {
            // Cleanup
            if (is_dir($tempDir)) {
                Process::run(['rm', '-rf', $tempDir]);
            }
        }
    }

    private function getBinaryDownloadUrl(string $os, string $arch): ?string
    {
        // whisper.cpp releases: https://github.com/ggerganov/whisper.cpp/releases
        // Only Windows binaries are available pre-built
        $baseUrl = 'https://github.com/ggerganov/whisper.cpp/releases/latest/download';

        return match ($os) {
            'windows' => match ($arch) {
                'x86_64', 'amd64' => "{$baseUrl}/whisper-bin-x64.zip",
                default => "{$baseUrl}/whisper-bin-Win32.zip",
            },
            // macOS and Linux need to be compiled from source
            'darwin', 'linux' => null,
            default => null,
        };
    }

    private function getDataDirectory(): string
    {
        $customPath = config('purrai.whisper.data_dir');
        if ($customPath) {
            return $customPath;
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? getenv('HOME') ?? getenv('USERPROFILE');

        if (! $home) {
            return storage_path('whisper');
        }

        return match ($this->getOS()) {
            'darwin' => $home.'/Library/Application Support/PurrAI/whisper',
            'windows' => ($_SERVER['LOCALAPPDATA'] ?? $_SERVER['APPDATA'] ?? $home.'/AppData/Local').'/PurrAI/whisper',
            default => $home.'/.local/share/purrai/whisper',
        };
    }

    private function getBinaryPath(): string
    {
        $customPath = config('purrai.whisper.binary_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $binaryName = $this->getOS() === 'windows' ? 'whisper.exe' : 'whisper';

        return $this->dataDir.'/bin/'.$binaryName;
    }

    private function getModelPath(): string
    {
        $customPath = config('purrai.whisper.model_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        return $this->dataDir.'/models/ggml-base.en.bin';
    }

    public function getFfmpegPath(): string
    {
        $customPath = config('purrai.whisper.ffmpeg_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        // Check if ffmpeg is in PATH
        $which = $this->getOS() === 'windows' ? 'where' : 'which';
        $result = Process::run([$which, 'ffmpeg']);

        if ($result->successful()) {
            return trim($result->output());
        }

        // Use local downloaded version
        $binaryName = $this->getOS() === 'windows' ? 'ffmpeg.exe' : 'ffmpeg';

        return "{$this->dataDir}/bin/{$binaryName}";
    }

    private function isFfmpegAvailable(): bool
    {
        $ffmpegPath = $this->getFfmpegPath();

        if (file_exists($ffmpegPath)) {
            return true;
        }

        $which = $this->getOS() === 'windows' ? 'where' : 'which';
        $result = Process::run([$which, 'ffmpeg']);

        return $result->successful();
    }

    private function getTempPath(string $prefix): string
    {
        return sys_get_temp_dir().'/'.$prefix.uniqid();
    }

    private function getOS(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'darwin',
            'Windows' => 'windows',
            default => 'linux',
        };
    }

    private function getArch(): string
    {
        $arch = php_uname('m');

        return match ($arch) {
            'arm64', 'aarch64' => 'arm64',
            'x86_64', 'amd64', 'AMD64' => 'x86_64',
            default => $arch,
        };
    }

    /**
     * Check if local speech configuration has pending setup
     * Returns true if configuration is incomplete or incorrect
     */
    public static function hasPendingConfiguration(): bool
    {
        try {
            // Check if speech-to-text is enabled
            if ((int) Setting::get('speech_to_text_enabled') !== 1) {
                return false;
            }

            // Check if local speech is enabled
            if ((int) Setting::get('use_local_speech') !== 1) {
                if (! Setting::getValidatedSpeechModel()) {
                    return true;
                }

                return false;
            }

            // Check if all required components are available
            $whisperService = app(self::class);
            $status = $whisperService->getStatus();

            // Return true if any component is missing
            return ! ($status['binary'] ?? false)
                || ! ($status['model'] ?? false)
                || ! ($status['ffmpeg'] ?? false);
        } catch (\Throwable) {
            // On any error, assume configuration is pending
            return true;
        }
    }
}
