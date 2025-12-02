<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\WhisperService;
use Illuminate\Console\Command;

final class SetupWhisperCommand extends Command
{
    protected $signature = 'whisper:setup {--model=base : Model to download} {--force : Force re-download even if files exist}';

    protected $description = 'Download and setup Whisper for speech recognition';

    public function handle(WhisperService $whisperService): int
    {
        $force = $this->option('force');
        $model = $this->option('model');

        $this->info('Setting up Whisper...');
        $this->newLine();

        try {
            $status = $whisperService->getStatus();

            // Check FFmpeg
            if (! $status['ffmpeg'] || $force) {
                $this->info('Downloading FFmpeg...');
                $whisperService->downloadFfmpeg();
                $this->line('✓ FFmpeg ready');
            } else {
                $this->line('✓ FFmpeg already installed');
            }

            // Check Binary
            if (! $status['binary'] || $force) {
                $this->info('Downloading Whisper binary...');
                $whisperService->downloadBinary();
                $this->line('✓ Whisper binary ready');
            } else {
                $this->line('✓ Whisper binary already installed');
            }

            // Check Model
            if (! $whisperService->hasModel($model) || $force) {
                $this->info("Downloading model: {$model}...");
                $whisperService->downloadModel($model);
                $this->line("✓ Model {$model} ready");
            } else {
                $this->line("✓ Model {$model} already installed");
            }

            $this->newLine();
            $this->info('✓ Whisper setup complete!');
            $this->newLine();

            // Show status
            $status = $whisperService->getStatus();
            $this->table(
                ['Component', 'Status'],
                [
                    ['FFmpeg', $status['ffmpeg'] ? '✓ Installed' : '✗ Missing'],
                    ['Binary', $status['binary'] ? '✓ Installed' : '✗ Missing'],
                    ['Model', $status['model'] ? "✓ {$status['current_model']}" : '✗ Missing'],
                    ['GPU Support', $status['gpu'] ? '✓ Available' : '✗ Not available'],
                ]
            );

            if (! empty($status['available_models'])) {
                $this->newLine();
                $this->info('Available models: '.implode(', ', $status['available_models']));
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Setup failed!');
            $this->line($e->getMessage());

            return self::FAILURE;
        }
    }
}
