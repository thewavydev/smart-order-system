<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreatedMail;
use App\Models\Order;

class SendOrderEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->order->customer_email)
                ->queue(new OrderCreatedMail($this->order));

            \Log::info("Order email queued for order #{$this->order->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send order email: {$e->getMessage()}");
        }
    }
}
