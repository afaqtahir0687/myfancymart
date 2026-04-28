<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\ProductNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendProductNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $type)
    {
        $this->product = $product;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Chunk users to prevent memory exhaustion
        User::chunk(500, function ($users) {
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new ProductNotificationMail($this->product, $this->type));
                } catch (\Exception $e) {
                    // Ignore exception if mail fails for a specific user
                }
            }
        });
    }
}
