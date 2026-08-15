<?php

namespace App\Jobs;

use App\Contracts\WhatsAppProviderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout for job execution (seconds).
     */
    public $timeout = 60;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $target,
        public string $message,
        public array $context = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppProviderInterface $provider): void
    {
        try {
            $result = $provider->sendMessage($this->target, $this->message, $this->context);

            if (!$result['success']) {
                Log::warning('WhatsApp message send failed', [
                    'target' => $this->target,
                    'message_preview' => substr($this->message, 0, 50),
                    'result' => $result,
                ]);

                // Retry if not successful
                if ($this->attempts() < $this->tries) {
                    $this->release(30); // Retry after 30 seconds
                }
            } else {
                Log::info('WhatsApp message sent successfully', [
                    'target' => $this->target,
                    'message_id' => $result['message_id'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp message send exception', [
                'target' => $this->target,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('WhatsApp message job failed permanently', [
            'target' => $this->target,
            'message_preview' => substr($this->message, 0, 100),
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
