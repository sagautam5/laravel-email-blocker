<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByEmailRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

it('allows all emails when blocked emails list is empty', function () {
    Config::set('email-blocker.settings.blocked_emails', []);

    $rule = new BlockByEmailRule();

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('blocks specific emails when blocked list is set and logs disabled', function () {
    Config::set('email-blocker.settings.blocked_emails', ['alice@example.com']);
    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByEmailRule();

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe(['bob@test.com']);
});

it('blocks specific emails when blocked list is set and logs enabled', function () {
    Config::set('email-blocker.settings.blocked_emails', ['alice@example.com']);
    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByEmailRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Test subject')
        ->html('<p>Hello</p>');

    $rule->setContext(new EmailContext($context), ReceiverType::TO);

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe(['bob@test.com']);

    assertDatabaseHas('blocked_emails', [
        'email' => 'alice@example.com',
    ]);
});

it('blocks all emails if all are blocked', function () {
    Config::set('email-blocker.settings.blocked_emails', ['alice@example.com', 'bob@test.com']);
    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByEmailRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Test subject')
        ->html('<p>Hello</p>');

    $rule->setContext(new EmailContext($context), ReceiverType::TO);

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);

    assertDatabaseHas('blocked_emails', ['email' => 'alice@example.com']);
    assertDatabaseHas('blocked_emails', ['email' => 'bob@test.com']);
});

it('returns correct block reason', function () {
    $rule = new BlockByEmailRule();

    expect($rule->getReason())->toBe('Sender email address is blocked by configuration.');
});

it('is case-sensitive and only blocks exact matches', function () {
    Config::set('email-blocker.settings.blocked_emails', ['Alice@example.com']);
    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByEmailRule();

    $emails = [
        'alice@example.com',
        'Alice@example.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe(['alice@example.com']);
});
