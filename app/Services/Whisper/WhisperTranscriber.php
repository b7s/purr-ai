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
            && file_exists($this->paths->getModelPath());
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
        $args = [
            $this->paths->getBinaryPath(),
            '-m', $this->paths->getModelPath(),
            '-f', $wavPath,
            '-l', 'en',
            '-nt',
            '--no-timestamps',
        ];

        if ($this->platform->hasGpuSupport()) {
            $args[] = '-ng';
        }

        $result = Process::timeout(120)->run($args);

        if (! $result->successful() && $this->platform->hasGpuSupport()) {
            Log::info('GPU transcription failed, falling back to CPU');
            $args = array_filter($args, fn ($arg) => $arg !== '-ng');
            $result = Process::timeout(120)->run($args);
        }

        if (! $result->successful()) {
            Log::error('Whisper transcription failed', ['error' => $result->errorOutput()]);

            return '';
        }

        return trim($result->output());
    }
}
