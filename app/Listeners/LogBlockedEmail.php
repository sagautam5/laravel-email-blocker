<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Sagautam5\LaravelEmailBlocker\App\Events\EmailBlockedEvent;

class LogBlockedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailBlockedEvent $event): void
    {
        //
    }
}
