<?php

namespace App\Listeners;

use App\Events\LogedUser;
use App\Mail\HelloUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserLogedNotification implements ShouldQueue
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
     * @param object $event
     *
     * @return void
     */
    public function handle(LogedUser $event)
    {

        $hello = new HelloUser($event->user);
        Mail::to($event->user)->send($hello);

        Log::info("Email has been send");
    }
}
