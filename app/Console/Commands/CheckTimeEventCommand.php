<?php

namespace App\Console\Commands;

use App\Jobs\CheckTimeEventJob;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTimeEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-time-event-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $events = Event::where('end_time', '<', $now)
            ->where('status', '!=', 0)
            ->get();

        foreach ($events as $event) {
            CheckTimeEventJob::dispatch($event->id);

            Log::info("Событие '{$event->title}' добавлено в очередь");
        }
    }
}
