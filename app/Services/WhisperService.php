<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use LaravelWhisper\Exceptions\WhisperException;
use LaravelWhisper\Whisper;

final class WhisperService
{
    private readonly Whisper $whisper;

    public function __construct()
    {
        $config = new \LaravelWhisper\Config(
            model: config('purrai.whisper.model', 'base'),
        );

        $this->whisper = new Whisper($config, new LaravelLogger);
    }

    public function transcribe(UploadedFile $audioFile): string
    {
        $tempPath = sys_get_temp_dir().'/purrai_audio_'.uniqid().'.'.$audioFile->getClientOriginalExtension();
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
     * Run complete Whisper setup using vendor binary
     *
     * @throws \Exception
     */
    public function runSetup(string $model = 'base', string $language = 'auto', int $timeout = 600): bool
    {
        $whisperSetupPath = base_path('vendor/bin/whisper-setup');

        if (! file_exists($whisperSetupPath)) {
            throw new \Exception("Whisper setup binary not found at: {$whisperSetupPath}");
        }

        $result = \Illuminate\Support\Facades\Process::timeout($timeout)
            ->path(base_path())
            ->run([
                PHP_BINARY,
                $whisperSetupPath,
                "--model={$model}",
                "--language={$language}",
            ]);

        if (! $result->successful()) {
            throw new \Exception($result->errorOutput() ?: 'Whisper setup failed');
        }

        return true;
    }

    /**
     * Remove existing Whisper installation
     */
    public function removeInstallation(): bool
    {
        $result = \Illuminate\Support\Facades\Process::run([
            'rm', '-rf', $_SERVER['HOME'].'/.local/share/laravelwhisper',
        ]);

        return $result->successful();
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
    public function downloadModel(string $model = 'base'): bool
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
