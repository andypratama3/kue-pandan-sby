<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout for job execution (seconds).
     */
    public $timeout = 120;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $orderId,
        public ?string $storePath = null
    ) {
        // storePath is optional - if provided, PDF will be saved to storage
        // Otherwise, it just generates and doesn't store (for immediate download)
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with([
            'customer',
            'items.product',
            'items.variant',
            'returns.returnedProducts.product',
            'returns.returnedProducts.variant',
            'region',
        ])->findOrFail($this->orderId);

        // Generate PDF
        $pdf = Pdf::loadView('dashboard.admin.orders.invoice', [
            'order' => $order,
        ]);

        // Store PDF if path is provided
        if ($this->storePath) {
            $pdfContent = $pdf->output();
            Storage::put($this->storePath, $pdfContent);
        }

        // Mark order as PDF generated (optional - add column if needed)
        // $order->update(['pdf_generated_at' => now()]);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
