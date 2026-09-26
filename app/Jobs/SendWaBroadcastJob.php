<?php

namespace App\Jobs;

use App\Models\WaBroadcast;
use App\Services\WaBroadcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWaBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $broadcast;
    public $selectedUserIds;

    /**
     * Create a new job instance.
     */
    public function __construct(WaBroadcast $broadcast, ?array $selectedUserIds = null)
    {
        $this->broadcast = $broadcast;
        $this->selectedUserIds = $selectedUserIds;
    }

    /**
     * Execute the job.
     */
    public function handle(WaBroadcastService $service): void
    {
        $service->executeBroadcast($this->broadcast, $this->selectedUserIds);
    }
}
