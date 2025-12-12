<?php

namespace Sagautam5\EmailBlocker\Listeners;

use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Facades\Logger;

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
        Logger::info($event->context, $event->class);
    }
}
