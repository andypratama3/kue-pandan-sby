<?php

namespace App\Console\Commands;

use App\Models\ChatbotConversation;
use Illuminate\Console\Command;

class CleanupExpiredConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:cleanup-expired
                            {--hours=24 : Hours after which conversations are considered expired}
                            {--dry-run : Show what would be cleaned without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired chatbot conversation states (older than 24 hours)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        $cutoffTime = now()->subHours($hours);

        $this->info("Cleaning up conversations older than {$hours} hours (before {$cutoffTime})");

        // Find expired conversations (using 'current_step' column, not 'state')
        $expiredQuery = ChatbotConversation::where('last_interaction_at', '<', $cutoffTime)
            ->whereIn('current_step', ['welcomed', 'browsing_catalog', 'awaiting_delivery_question'])
            ->whereNotNull('current_step');

        $count = $expiredQuery->count();

        if ($count === 0) {
            $this->info('No expired conversations found.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("[DRY RUN] Would reset state for {$count} expired conversations");
            
            $sample = $expiredQuery->limit(5)->get(['id', 'sender_number', 'current_step', 'last_interaction_at']);
            $this->table(
                ['ID', 'Sender', 'Step', 'Last Interaction'],
                $sample->map(fn($c) => [
                    $c->id,
                    $c->sender_number,
                    $c->current_step,
                    $c->last_interaction_at?->diffForHumans() ?? 'never'
                ])
            );

            return self::SUCCESS;
        }

        // Reset expired conversations to 'idle' state
        $updated = $expiredQuery->update([
            'current_step' => 'idle',
            'context_data' => null,
            'updated_at' => now(),
        ]);

        $this->info("✅ Successfully reset {$updated} expired conversations to 'idle' state.");

        return self::SUCCESS;
    }
}
