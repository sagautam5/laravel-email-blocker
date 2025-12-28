<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Listeners\LogBlockedEmail;
use Sagautam5\EmailBlocker\Providers\EmailBlockServiceProvider;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;

it('fires the LogBlockedEmail listener when EmailBlockedEvent is dispatched', function () {
    Event::fake();

    $context = new BlockedEmailContext(
        email: 'test@example.com',
        reason: 'global block',
        rule: 'BlockByGlobalRule',
        context: null,
        receiver_type: null
    );

    Event::dispatch(new EmailBlockedEvent($context));

    Event::assertListening(
        EmailBlockedEvent::class,
        LogBlockedEmail::class
    );
});

it('registers config publish path', function () {
    app()->register(EmailBlockServiceProvider::class);

    $paths = ServiceProvider::pathsToPublish(
        EmailBlockServiceProvider::class,
        'email-blocker-config'
    );

    expect($paths)->toBeArray()
        ->and($paths)->toHaveCount(1);
});

it('registers migration publish path', function () {
    app()->register(EmailBlockServiceProvider::class);

    $paths = ServiceProvider::pathsToPublish(
        EmailBlockServiceProvider::class,
        'email-blocker-migrations'
    );

    expect($paths)->toBeArray()
        ->and($paths)->toHaveCount(1);
});
