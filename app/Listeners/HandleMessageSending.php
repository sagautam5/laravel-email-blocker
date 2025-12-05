<?php

namespace Sagautam5\EmailBlocker\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Queue\InteractsWithQueue;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Illuminate\Pipeline\Pipeline;
use Sagautam5\EmailBlocker\Rules\BlockByDomainRule;
use Sagautam5\EmailBlocker\Rules\BlockByEnvironmentRule;
use Sagautam5\EmailBlocker\Rules\BlockByMailableRule;
use Sagautam5\EmailBlocker\Rules\BlockGloballlyRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;

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
    public function handle(MessageSending $event)
    {
        $context = new EmailContext();

        return app(Pipeline::class)
            ->send($context)
            ->through(config('email-blocker.rules'))
            ->then(function (EmailContext $context) use ($event) {
                return true;
            });
    }
}
