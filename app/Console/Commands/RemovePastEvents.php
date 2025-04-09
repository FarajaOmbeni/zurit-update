<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\PastEvent;
use Illuminate\Console\Command;

class RemovePastEvents extends Command
{
    protected $signature = 'remove:past-events';

    protected $description = 'Remove events that have already happened';

    public function handle()
    {
        $events = Event::where('date', '<', now())->get();

        foreach ($events as $event) {
            PastEvent::create([
                'name' => $event->name,
                'date' => $event->date,
                'image' => $event->image,
            ]);
        }
        $event->delete();
    }
}
