<?php

namespace Sagautam5\LaravelEmailBlocker\App\Providers;

use App\Listeners\LogBlockedEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Sagautam5\LaravelEmailBlocker\App\Events\EmailBlockedEvent;

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