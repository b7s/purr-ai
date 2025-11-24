<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearNativeCache extends Command
{
    protected $signature = 'native:clear';

    protected $description = 'Clear all NativePHP and application caches for fresh start';

    public function handle(): int
    {
        $this->info('Clearing NativePHP caches...');

        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('cache:clear');
        $this->call('optimize:clear');

        $nativeCachePaths = [
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            storage_path('framework/sessions'),
            storage_path('logs'),
            base_path('vendor/nativephp/desktop/resources/js/dist'),
            base_path('bootstrap/cache/packages.php'),
            base_path('bootstrap/cache/services.php'),
        ];

        foreach ($nativeCachePaths as $path) {
            if (File::exists($path)) {
                if (File::isDirectory($path)) {
                    File::cleanDirectory($path);
                    $this->line("Cleared directory: {$path}");
                } else {
                    File::delete($path);
                    $this->line("Deleted file: {$path}");
                }
            }
        }

        $this->info('Rebuilding autoloader...');
        exec('composer dump-autoload -q');

        $this->newLine();
        $this->info('✓ All caches cleared successfully!');
        $this->info('Run "composer native:dev" to start with fresh cache.');

        return self::SUCCESS;
    }
}
