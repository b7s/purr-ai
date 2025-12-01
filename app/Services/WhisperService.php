<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use App\Services\Whisper\WhisperDownloader;
use App\Services\Whisper\WhisperPathResolver;
use App\Services\Whisper\WhisperPlatformDetector;
use App\Services\Whisper\WhisperTranscriber;
use Illuminate\Http\UploadedFile;

final class WhisperService
{
    private readonly WhisperPlatformDetector $platform;

    private readonly WhisperPathResolver $paths;

    private readonly WhisperDownloader $downloader;

    private readonly WhisperTranscriber $transcriber;

    public function __construct()
    {
        $this->platform = new WhisperPlatformDetector;
        $this->paths = new WhisperPathResolver($this->platform);
        $this->downloader = new WhisperDownloader($this->platform, $this->paths);
        $this->transcriber = new WhisperTranscriber($this->platform, $this->paths);
    }

    public function transcribe(UploadedFile $audioFile): string
    {
        return $this->transcriber->transcribe($audioFile);
    }

    public function transcribeFromPath(string $audioPath): string
    {
        return $this->transcriber->transcribeFromPath($audioPath);
    }

    public function isAvailable(): bool
    {
        return $this->transcriber->isAvailable();
    }

    public function hasGpuSupport(): bool
    {
        return $this->platform->hasGpuSupport();
    }

    /**
     * @return array{binary: bool, model: bool, ffmpeg: bool, gpu: bool}
     */
    public function getStatus(): array
    {
        return [
            'binary' => file_exists($this->paths->getBinaryPath()),
            'model' => file_exists($this->paths->getModelPath()),
            'ffmpeg' => $this->downloader->isFfmpegAvailable(),
            'gpu' => $this->platform->hasGpuSupport(),
        ];
    }

    public function setup(): bool
    {
        $this->paths->ensureDirectoriesExist();

        $ffmpegDownloaded = $this->downloader->downloadFfmpeg();
        $binaryDownloaded = $this->downloader->downloadBinary();
        $modelDownloaded = $this->downloader->downloadModel();

        return $ffmpegDownloaded && $binaryDownloaded && $modelDownloaded;
    }

    public function downloadFfmpeg(): bool
    {
        return $this->downloader->downloadFfmpeg();
    }

    public function downloadBinary(): bool
    {
        $result = $this->downloader->downloadBinary();

        if ($result) {
            $this->downloader->fixLibrarySymlinks();
        }

        return $result;
    }

    public function fixLibrarySymlinks(): void
    {
        $this->downloader->fixLibrarySymlinks();
    }

    public function downloadModel(string $model = 'base.en'): bool
    {
        return $this->downloader->downloadModel($model);
    }

    public function getFfmpegPath(): string
    {
        return $this->paths->getFfmpegPath();
    }

    public static function hasPendingConfiguration(): bool
    {
        try {
            if ((int) Setting::get('speech_to_text_enabled') !== 1) {
                return false;
            }

            if ((int) Setting::get('use_local_speech') !== 1) {
                if (! Setting::getValidatedSpeechModel()) {
                    return true;
                }

                return false;
            }

            $whisperService = app(self::class);
            $status = $whisperService->getStatus();

            return ! $status['binary']
                || ! $status['model']
                || ! $status['ffmpeg'];
        } catch (\Throwable) {
            return true;
        }
    }
}
