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
        $force = $this->option('force');

        $this->info('Setting up Whisper using laravelwhisper package...');
        $this->newLine();

        if ($force) {
            $this->warn('Force mode: removing existing installation...');

            if (! $whisperService->removeInstallation()) {
                $this->error('Failed to remove existing installation');

                return self::FAILURE;
            }
        }

        $this->info('Running vendor/bin/whisper-setup --model=base --language=auto');
        $this->newLine();

        try {
            $whisperService->runSetup();

            $this->newLine();
            $this->info('✓ Whisper setup complete!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Setup failed!');
            $this->line($e->getMessage());

            return self::FAILURE;
        }
    }
}
