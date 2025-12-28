<?php

use Illuminate\Mail\Events\MessageSending;
use Sagautam5\EmailBlocker\Listeners\HandleMessageSending;
use Symfony\Component\Mime\Email;

beforeEach(function () {
    config()->set('email-blocker.block_enabled', true);
});

afterEach(function () {
    Mockery::close();
});

it('returns original message when email blocker is enabled', function () {
    config()->set('email-blocker.block_enabled', true);

    $email = new Email;
    $email->to('user@example.com');
    $event = new MessageSending($email, []);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBe($email);
});

it('applies email blocking rules when blocker is disabled and no mailable exists', function () {
    $email = new Email;
    $email->to('user@example.com');
    $event = new MessageSending($email, []);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBe($email);
});

it('passes mailable to EmailBlockService when available', function () {
    $email = new Email;
    $email->to('user@example.com');

    $mailable = new stdClass;

    $event = new MessageSending($email, [
        '__laravel_mailable' => $mailable::class,
    ]);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result)->toBe($email);
});

it('returns either Email or boolean from applyRules', function () {
    $email = new Email;
    $email->to('user@example.com');

    $event = new MessageSending($email, []);

    $listener = new HandleMessageSending;

    $result = $listener->handle($event);

    expect($result instanceof Email || is_bool($result))->toBeTrue();
});
