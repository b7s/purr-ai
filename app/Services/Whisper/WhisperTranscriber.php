<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

final class WhisperTranscriber
{
    public function __construct(
        private readonly WhisperPlatformDetector $platform,
        private readonly WhisperPathResolver $paths,
    ) {}

    public function transcribe(UploadedFile $audioFile): string
    {
        if (! $this->isAvailable()) {
            Log::warning('Whisper not available, returning empty transcription');

            return '';
        }

        $tempWavPath = $this->convertToWav($audioFile);

        try {
            return $this->runWhisper($tempWavPath);
        } finally {
            @unlink($tempWavPath);
        }
    }

    public function transcribeFromPath(string $audioPath): string
    {
        if (! $this->isAvailable()) {
            return '';
        }

        $tempWavPath = $this->convertFileToWav($audioPath);

        try {
            return $this->runWhisper($tempWavPath);
        } finally {
            @unlink($tempWavPath);
        }
    }

    public function isAvailable(): bool
    {
        return file_exists($this->paths->getBinaryPath())
            && file_exists($this->paths->getModelPath())
            && $this->hasRequiredLibraries();
    }

    private function hasRequiredLibraries(): bool
    {
        if ($this->platform->isWindows()) {
            return true;
        }

        $binaryPath = $this->paths->getBinaryPath();
        $env = $this->getWhisperEnvironment();

        $result = Process::env($env)->timeout(5)->run([$binaryPath, '--help']);

        if ($result->exitCode() === 127) {
            return false;
        }

        if (str_contains($result->errorOutput(), 'cannot open shared object file')) {
            return false;
        }

        return true;
    }

    private function convertToWav(UploadedFile $audioFile): string
    {
        $tempInputPath = $this->paths->getTempPath('audio_input_').'.webm';
        $audioFile->move(dirname($tempInputPath), basename($tempInputPath));

        return $this->convertFileToWav($tempInputPath);
    }

    private function convertFileToWav(string $inputPath): string
    {
        $tempWavPath = $this->paths->getTempPath('audio_wav_').'.wav';
        $ffmpegPath = $this->paths->getFfmpegPath();

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
            Log::error('FFmpeg conversion failed', ['error' => $result->errorOutput()]);
            throw new RuntimeException('Failed to convert audio file');
        }

        return $tempWavPath;
    }

    private function runWhisper(string $wavPath): string
    {
        $binaryPath = $this->paths->getBinaryPath();
        $modelPath = $this->paths->getModelPath();

        if (! file_exists($binaryPath)) {
            Log::error('Whisper binary not found', ['path' => $binaryPath]);

            return '';
        }

        if (! file_exists($modelPath)) {
            Log::error('Whisper model not found', ['path' => $modelPath]);

            return '';
        }

        if (! file_exists($wavPath)) {
            Log::error('Audio file not found', ['path' => $wavPath]);

            return '';
        }

        $args = [
            $binaryPath,
            '-m', $modelPath,
            '-f', $wavPath,
            '-l', 'en',
            '-nt',
            '--no-timestamps',
        ];

        if ($this->platform->hasGpuSupport()) {
            $args[] = '-ng';
        }

        Log::info('Running Whisper transcription', [
            'binary' => $binaryPath,
            'model' => $modelPath,
            'audio' => $wavPath,
            'gpu' => $this->platform->hasGpuSupport(),
        ]);

        $env = $this->getWhisperEnvironment();
        $result = Process::timeout(120)->env($env)->run($args);

        if (! $result->successful() && $this->platform->hasGpuSupport()) {
            Log::info('GPU transcription failed, falling back to CPU');
            $args = array_filter($args, fn ($arg) => $arg !== '-ng');
            $result = Process::timeout(120)->env($env)->run($args);
        }

        if (! $result->successful()) {
            Log::error('Whisper transcription failed', [
                'exit_code' => $result->exitCode(),
                'error_output' => $result->errorOutput(),
                'standard_output' => $result->output(),
                'command' => implode(' ', $args),
            ]);

            return '';
        }

        $transcription = trim($result->output());
        Log::info('Whisper transcription completed', [
            'length' => strlen($transcription),
            'preview' => substr($transcription, 0, 100),
        ]);

        return $transcription;
    }

    /**
     * @return array<string, string>
     */
    private function getWhisperEnvironment(): array
    {
        if ($this->platform->isWindows()) {
            return [];
        }

        $binDir = dirname($this->paths->getBinaryPath());
        $libDir = "{$binDir}/../lib";

        $env = [];

        if (is_dir($libDir)) {
            if ($this->platform->isMacOS()) {
                $currentPath = getenv('DYLD_LIBRARY_PATH') ?: '';
                $env['DYLD_LIBRARY_PATH'] = $currentPath ? "{$libDir}:{$currentPath}" : $libDir;
            } else {
                $currentPath = getenv('LD_LIBRARY_PATH') ?: '';
                $env['LD_LIBRARY_PATH'] = $currentPath ? "{$libDir}:{$currentPath}" : $libDir;
            }
        }

        return $env;
    }
}
