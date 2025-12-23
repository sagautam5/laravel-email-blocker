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
        if (config('email-blocker.block_enabled') == true) {
            return $event->message;
        }

        $mailable = $event->data['__laravel_mailable'] ?? null;

        return (new EmailBlockService($event->message, $mailable))->applyRules();
    }
}
