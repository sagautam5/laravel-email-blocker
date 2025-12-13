<?php

namespace Sagautam5\EmailBlocker\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Sagautam5\EmailBlocker\Services\EmailBlockService;
use Symfony\Component\Mime\Email;

class HandleMessageSending
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
    public function handle(MessageSending $event): Email|bool
    {
        dd((new EmailBlockService($event->message))->applyRules());

        return (new EmailBlockService($event->message))->applyRules();
    }
}
