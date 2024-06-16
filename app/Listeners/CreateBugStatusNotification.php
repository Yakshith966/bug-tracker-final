<?php

namespace App\Listeners;

use App\Events\BugStatusChanged;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateBugStatusNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BugStatusChanged $event)
    {
        // dd($event);
        // Extract the bug from the event
        // Create a new notification
        Notification::create([
            'user_id' => $event->userId, // Use the user ID from the event
            'title' => $event->title, // Use the title from the event
            'description' => $event->description, // Use the description from the event
            'status' => $event->status, // Use the status from the event
            'project_id'=>$event->projectId,
            'date' => now(), // Use the current date and time
        ]);
    }
}
