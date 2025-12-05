<?php

namespace Sagautam5\EmailBlocker\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Listeners\HandleMessageSending;
use Sagautam5\EmailBlocker\Listeners\LogBlockedEmail;

class EmailEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        MessageSending::class => [
            HandleMessageSending::class
        ],
        EmailBlockedEvent::class => [
            LogBlockedEmail::class
        ]
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;
}