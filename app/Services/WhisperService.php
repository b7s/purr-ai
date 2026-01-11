<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use App\Services\Whisper\WhisperDownloadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use WhisperPHP\Config;
use WhisperPHP\Whisper;

final class WhisperService
{
    private Whisper $whisper;

    public function __construct()
    {
        $config = new Config(
            dataDir: $this->getDataDirectory(),
            model: 'base',
            language: 'auto',
        );

        $logger = app(\App\Services\LaravelLogger::class);
        $this->whisper = new Whisper($config, $logger);
    }

    public function transcribe(UploadedFile $audioFile): string
    {
        if (! $this->isAvailable()) {
            return '';
        }

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = uniqid('audio_', true).'.'.$audioFile->getClientOriginalExtension();
        $fullPath = $tempDir.'/'.$filename;

        $audioFile->move($tempDir, $filename);

        if (! file_exists($fullPath)) {
            Log::error('Audio file not found after move', [
                'full_path' => $fullPath,
                'temp_dir' => $tempDir,
                'filename' => $filename,
            ]);

            return '';
        }

        try {
            $result = $this->whisper->audio($fullPath)->toText();

            return $result;
        } catch (\Exception $e) {
            Log::error('Whisper transcription failed', [
                'error' => $e->getMessage(),
                'file' => $fullPath,
                'exists' => file_exists($fullPath),
            ]);

            return '';
        } finally {
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    public function transcribeFromPath(string $audioPath): string
    {
        if (! $this->isAvailable()) {
            return '';
        }

        try {
            return $this->whisper->audio($audioPath)->toText();
        } catch (\Exception $e) {
            Log::error('Whisper transcription failed', ['error' => $e->getMessage()]);

            return '';
        }
    }

    public function isAvailable(): bool
    {
        return $this->whisper->isAvailable();
    }

    public function hasGpuSupport(): bool
    {
        return $this->whisper->hasGpuSupport();
    }

    /**
     * @return array{binary: bool, model: bool, current_model: string, available_models: array<string>, ffmpeg: bool, gpu: bool}
     */
    public function getStatus(): array
    {
        return $this->whisper->getStatus();
    }

    /**
     * @throws WhisperDownloadException
     */
    public function setup(): bool
    {
        try {
            return $this->whisper->setup();
        } catch (\Exception $e) {
            throw new WhisperDownloadException($e->getMessage());
        }
    }

    /**
     * @throws WhisperDownloadException
     */
    public function downloadFfmpeg(): bool
    {
        try {
            return $this->whisper->downloadFfmpeg();
        } catch (\Exception $e) {
            throw new WhisperDownloadException('Failed to download FFmpeg', $e->getMessage());
        }
    }

    /**
     * @throws WhisperDownloadException
     */
    public function downloadBinary(): bool
    {
        try {
            return $this->whisper->downloadBinary();
        } catch (\Exception $e) {
            throw new WhisperDownloadException('Failed to download Whisper binary', $e->getMessage());
        }
    }

    /**
     * @throws WhisperDownloadException
     */
    public function downloadModel(string $model = 'base'): bool
    {
        try {
            return $this->whisper->downloadModel($model);
        } catch (\Exception $e) {
            throw new WhisperDownloadException('Failed to download Whisper model', $e->getMessage());
        }
    }

    public function useModel(string $model): self
    {
        $this->whisper->useModel($model);

        return $this;
    }

    public function getCurrentModel(): string
    {
        return $this->whisper->getCurrentModel();
    }

    /**
     * @return array<string>
     */
    public function getAvailableModels(): array
    {
        return $this->whisper->getAvailableModels();
    }

    public function hasModel(string $model): bool
    {
        return $this->whisper->hasModel($model);
    }

    public function getFfmpegPath(): string
    {
        return $this->whisper->getFfmpegPath();
    }

    public function getModelPath(?string $model = null): string
    {
        return $this->whisper->getModelPath($model);
    }

    public function deleteModel(string $model): bool
    {
        return $this->whisper->deleteModel($model);
    }

    /**
     * @throws WhisperDownloadException
     */
    public function redownloadModel(string $model): bool
    {
        try {
            return $this->whisper->redownloadModel($model);
        } catch (\Exception $e) {
            throw new WhisperDownloadException('Failed to redownload model', $e->getMessage());
        }
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

    private function getDataDirectory(): string
    {
        $customPath = config('purrai.whisper.data_dir');
        if ($customPath) {
            return $customPath;
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? getenv('HOME') ?: getenv('USERPROFILE');

        if (! $home) {
            return storage_path('whisper');
        }

        $os = PHP_OS_FAMILY;

        return match ($os) {
            'Darwin' => "{$home}/Library/Application Support/PurrAI/whisper",
            'Windows' => ($_SERVER['LOCALAPPDATA'] ?? $_SERVER['APPDATA'] ?? "{$home}/AppData/Local").'/PurrAI/whisper',
            default => "{$home}/.local/share/purrai/whisper",
        };
    }
}
