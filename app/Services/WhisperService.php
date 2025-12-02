<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use LaravelWhisper\Config;
use LaravelWhisper\Exceptions\WhisperException;
use LaravelWhisper\WhisperService as BaseWhisperService;

final class WhisperService
{
    private readonly BaseWhisperService $whisper;

    public function __construct()
    {
        $config = new Config(
            dataDir: $this->resolveDataDirectory(),
            binaryPath: config('purrai.whisper.binary_path'),
            modelPath: config('purrai.whisper.model_path'),
            ffmpegPath: config('purrai.whisper.ffmpeg_path'),
            model: config('purrai.whisper.model', 'base'),
            language: config('purrai.whisper.language', 'auto'),
        );

        $this->whisper = new BaseWhisperService($config, new LaravelLogger);
    }

    private function resolveDataDirectory(): ?string
    {
        $configDir = config('purrai.whisper.data_dir');
        if ($configDir) {
            return $configDir;
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? getenv('HOME') ?: getenv('USERPROFILE');

        if (! $home) {
            return null;
        }

        return match (PHP_OS_FAMILY) {
            'Darwin' => "{$home}/Library/Application Support/PurrAI/whisper",
            'Windows' => ($_SERVER['LOCALAPPDATA'] ?? $_SERVER['APPDATA'] ?? "{$home}/AppData/Local").'/PurrAI/whisper',
            default => "{$home}/.local/share/purrai/whisper",
        };
    }

    public function transcribe(UploadedFile $audioFile): string
    {
        $tempPath = sys_get_temp_dir().'/audio_'.uniqid().'.'.$audioFile->getClientOriginalExtension();
        $audioFile->move(\dirname($tempPath), basename($tempPath));

        try {
            return $this->whisper->audio($tempPath)->text();
        } finally {
            @unlink($tempPath);
        }
    }

    public function transcribeFromPath(string $audioPath): string
    {
        return $this->whisper->audio($audioPath)->text();
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
     * @return array{binary: bool, model: bool, ffmpeg: bool, gpu: bool}
     */
    public function getStatus(): array
    {
        return $this->whisper->getStatus();
    }

    /**
     * @throws WhisperException
     */
    public function setup(): bool
    {
        return $this->whisper->setup();
    }

    /**
     * @throws WhisperException
     */
    public function downloadFfmpeg(): bool
    {
        return $this->whisper->downloadFfmpeg();
    }

    /**
     * @throws WhisperException
     */
    public function downloadBinary(): bool
    {
        return $this->whisper->downloadBinary();
    }

    public function fixLibrarySymlinks(): void
    {
        $this->whisper->fixLibrarySymlinks();
    }

    /**
     * @throws WhisperException
     */
    public function downloadModel(string $model = 'base.en'): bool
    {
        return $this->whisper->downloadModel($model);
    }

    public function getFfmpegPath(): string
    {
        return $this->whisper->getFfmpegPath();
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
