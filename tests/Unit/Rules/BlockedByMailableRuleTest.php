<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByMailableRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

/*
|--------------------------------------------------------------------------
| Test Mailables
|--------------------------------------------------------------------------
*/
class AllowedTestMailable {}
class BlockedTestMailable {}

it('allows all emails when blocked mailables list is empty', function () {
    Config::set('email-blocker.settings.blocked_mailables', []);

    $rule = new BlockByMailableRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Test')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context, AllowedTestMailable::class),
        ReceiverType::TO
    );

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('allows emails when mailable is not blocked', function () {
    Config::set('email-blocker.settings.blocked_mailables', [
        BlockedTestMailable::class,
    ]);

    $rule = new BlockByMailableRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Test')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context, AllowedTestMailable::class),
        ReceiverType::TO
    );

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('blocks all emails when mailable is blocked and logging disabled', function () {
    Config::set('email-blocker.settings.blocked_mailables', [
        BlockedTestMailable::class,
    ]);

    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByMailableRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Blocked mail')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context, BlockedTestMailable::class),
        ReceiverType::TO
    );

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('blocks all emails when mailable is blocked and logs enabled', function () {
    Config::set('email-blocker.settings.blocked_mailables', [
        BlockedTestMailable::class,
    ]);

    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByMailableRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Blocked mail')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context, BlockedTestMailable::class),
        ReceiverType::TO
    );

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);

    assertDatabaseHas('blocked_emails', [
        'email' => 'alice@example.com',
    ]);

    assertDatabaseHas('blocked_emails', [
        'email' => 'bob@test.com',
    ]);
});

it('returns correct block reason', function () {
    Config::set('email-blocker.settings.blocked_mailables', [
        BlockedTestMailable::class,
    ]);

    $rule = new BlockByMailableRule();

    $context = (new Email())
        ->from('system@example.com')
        ->to('alice@example.com')
        ->subject('Blocked mail')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context, BlockedTestMailable::class),
        ReceiverType::TO
    );

    expect($rule->getReason())->toBe(
        'The mailable "BlockedTestMailable" is blocked from being sent.'
    );
});
