<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneOldConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversations:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old conversations based on retention settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysToKeep = (int) Setting::get('delete_old_messages_days', 0);

        if ($daysToKeep < 1) {
            $this->info('Message retention is disabled. No conversations were removed.');
            return self::SUCCESS;
        }

        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        $this->info(sprintf(
            'Removing conversations older than %s...',
            $cutoffDate->format('Y-m-d H:i:s')
        ));

        try {
            $deleted = Conversation::where('updated_at', '<', $cutoffDate)
                ->orWhere('created_at', '<', $cutoffDate)
                ->delete();

            $this->info(sprintf('Successfully removed %d old conversations.', $deleted));
            Log::info(sprintf(
                'Removed %d old conversations (older than %s)',
                $deleted,
                $cutoffDate->format('Y-m-d H:i:s')
            ));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $errorMessage = 'Error removing old conversations: ' . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage, [
                'exception' => $e
            ]);

            return self::FAILURE;
        }
    }
}
