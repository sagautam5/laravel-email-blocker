<?php

use Illuminate\Mail\Events\MessageSending;
use Sagautam5\EmailBlocker\Listeners\HandleMessageSending;
use Sagautam5\EmailBlocker\Services\EmailBlockService;
use Symfony\Component\Mime\Email;

beforeEach(function () {
    config()->set('email-blocker.block_enabled', false);
});

afterEach(function () {
    Mockery::close();
});

it('returns original message when email blocker is enabled', function () {
    config()->set('email-blocker.block_enabled', true);

    $email = new Email;
    $event = new MessageSending($email, []);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBe($email);
});

it('applies email blocking rules when blocker is disabled and no mailable exists', function () {
    $email = new Email;
    $event = new MessageSending($email, []);

    $service = Mockery::mock('overload:'.EmailBlockService::class);
    $service
        ->shouldReceive('__construct')
        ->with($email, null)
        ->once();

    $service
        ->shouldReceive('applyRules')
        ->once()
        ->andReturn($email);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBe($email);
});

it('passes mailable to EmailBlockService when available', function () {
    $email = new Email;
    $mailable = new stdClass;

    $event = new MessageSending($email, [
        '__laravel_mailable' => $mailable,
    ]);

    $service = Mockery::mock('overload:'.EmailBlockService::class);
    $service
        ->shouldReceive('__construct')
        ->with($email, $mailable)
        ->once();

    $service
        ->shouldReceive('applyRules')
        ->once()
        ->andReturn(false);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBeFalse();
});

it('returns either Email or boolean from applyRules', function () {
    $email = new Email;
    $event = new MessageSending($email, []);

    $service = Mockery::mock('overload:'.EmailBlockService::class);
    $service
        ->shouldReceive('applyRules')
        ->once()
        ->andReturn(true);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result instanceof Email || is_bool($result))->toBeTrue();
});
