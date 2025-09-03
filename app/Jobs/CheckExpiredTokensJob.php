<?php

namespace App\Jobs;

use App\Models\UploadFileToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckExpiredTokensJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // check pending status and expired tokens
        UploadFileToken::where('status', UploadFileToken::STATUS_PENDING)
            ->where('expires_at', '<', now())
            ->update(['status' => UploadFileToken::STATUS_EXPIRED]);
    }
}
