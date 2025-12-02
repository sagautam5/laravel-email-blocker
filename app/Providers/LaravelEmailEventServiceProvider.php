<?php

namespace Sagautam5\LaravelEmailBlocker\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Sagautam5\LaravelEmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\LaravelEmailBlocker\Listeners\LogBlockedEmail;

class LaravelEmailEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        MessageSent::class => [
            
        ],
        MessageSending::class => [
            
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