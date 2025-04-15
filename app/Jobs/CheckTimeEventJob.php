<?php

namespace App\Jobs;

use App\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class CheckTimeEventJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $eventId = null)
    {
        $this->onConnection('database');
        $this->onQueue("default");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $event = Event::find($this->eventId);
            $event->update([
                'status' => 'inactive'
            ]);
        }catch (Throwable $exception){
            throw $exception;
        }
    }
}
