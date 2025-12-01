<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Illuminate\Support\Facades\Process;

final class WhisperPathResolver
{
    private string $dataDir;

    public function __construct(private readonly WhisperPlatformDetector $platform)
    {
        $this->dataDir = $this->resolveDataDirectory();
    }

    public function getDataDirectory(): string
    {
        return $this->dataDir;
    }

    public function getBinaryPath(): string
    {
        $customPath = config('purrai.whisper.binary_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $binaryName = $this->platform->isWindows() ? 'whisper.exe' : 'whisper';

        return "{$this->dataDir}/bin/{$binaryName}";
    }

    public function getModelPath(): string
    {
        $customPath = config('purrai.whisper.model_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        return "{$this->dataDir}/models/ggml-base.en.bin";
    }

    public function getFfmpegPath(): string
    {
        $customPath = config('purrai.whisper.ffmpeg_path');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $which = $this->platform->isWindows() ? 'where' : 'which';
        $result = Process::run([$which, 'ffmpeg']);

        if ($result->successful()) {
            return trim($result->output());
        }

        $binaryName = $this->platform->isWindows() ? 'ffmpeg.exe' : 'ffmpeg';

        return "{$this->dataDir}/bin/{$binaryName}";
    }

    public function getTempPath(string $prefix): string
    {
        return sys_get_temp_dir().'/'.$prefix.uniqid();
    }

    public function ensureDirectoriesExist(): void
    {
        $dirs = [
            $this->dataDir,
            dirname($this->getBinaryPath()),
            dirname($this->getModelPath()),
            dirname($this->getFfmpegPath()),
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    private function resolveDataDirectory(): string
    {
        $customPath = config('purrai.whisper.data_dir');
        if ($customPath) {
            return $customPath;
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? getenv('HOME') ?: getenv('USERPROFILE');

        if (! $home) {
            return storage_path('whisper');
        }

        return match ($this->platform->getOS()) {
            'darwin' => "{$home}/Library/Application Support/PurrAI/whisper",
            'windows' => ($_SERVER['LOCALAPPDATA'] ?? $_SERVER['APPDATA'] ?? "{$home}/AppData/Local").'/PurrAI/whisper',
            default => "{$home}/.local/share/purrai/whisper",
        };
    }
}
