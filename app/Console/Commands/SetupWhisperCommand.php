<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\WhisperService;
use Illuminate\Console\Command;

final class SetupWhisperCommand extends Command
{
    protected $signature = 'whisper:setup {--force : Force re-download even if files exist}';

    protected $description = 'Download and setup Whisper for speech recognition';

    public function handle(WhisperService $whisperService): int
    {
        $this->info('Checking Whisper status...');

        $status = $whisperService->getStatus();

        $this->table(
            ['Component', 'Status'],
            [
                ['FFmpeg', $status['ffmpeg'] ? '✓ Installed' : '✗ Missing'],
                ['Whisper Binary', $status['binary'] ? '✓ Installed' : '✗ Missing'],
                ['Whisper Model', $status['model'] ? '✓ Installed' : '✗ Missing'],
                ['GPU Support', $status['gpu'] ? '✓ Available' : '○ Not available'],
            ]
        );

        $force = $this->option('force');

        if ($status['binary'] && $status['model'] && $status['ffmpeg'] && ! $force) {
            $this->info('Verifying library dependencies...');
            $whisperService->fixLibrarySymlinks();

            if ($whisperService->isAvailable()) {
                $this->info('Whisper is already set up and ready to use!');

                return self::SUCCESS;
            }

            $this->warn('Library dependencies need to be fixed. Reinstalling...');
            $force = true;
        }

        if (! $status['ffmpeg'] || $force) {
            $this->info('Downloading FFmpeg...');
            $this->info('This may take a few minutes depending on your connection...');

            if ($whisperService->downloadFfmpeg()) {
                $this->info('✓ FFmpeg downloaded successfully');
            } else {
                $this->warn('✗ Failed to download FFmpeg automatically');
                $this->warn('Please install FFmpeg manually:');
                $this->line('  macOS: brew install ffmpeg');
                $this->line('  Ubuntu/Debian: sudo apt install ffmpeg');
                $this->line('  Windows: Download from https://ffmpeg.org/download.html');
            }
        }

        if (! $status['binary'] || $force) {
            $this->info(__('settings.other.downloading_whisper_binary'));

            if ($whisperService->downloadBinary()) {
                $this->info('✓ Whisper: '.__('settings.other.download_complete'));
            } else {
                $this->error('✗ '.__('settings.other.whisper_binary_download_failed'));

                return self::FAILURE;
            }
        }

        if (! $status['model'] || $force) {
            $model = config('purrai.whisper.model', 'base.en');
            $this->info("Downloading Whisper model ({$model})...");
            $this->info('This may take a few minutes depending on your connection...');

            if ($whisperService->downloadModel($model)) {
                $this->info('✓ Whisper model downloaded successfully');
            } else {
                $this->error('✗ Failed to download Whisper model');

                return self::FAILURE;
            }
        }

        $this->info('Verifying library dependencies...');
        $whisperService->fixLibrarySymlinks();

        if (! $whisperService->isAvailable()) {
            $this->error('✗ Whisper binary has missing library dependencies');
            $this->warn('Try running: php artisan whisper:setup --force');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Whisper setup complete!');

        $newStatus = $whisperService->getStatus();
        if ($newStatus['gpu']) {
            $this->info('GPU acceleration is available and will be used for faster transcription.');
        }

        return self::SUCCESS;
    }
}
