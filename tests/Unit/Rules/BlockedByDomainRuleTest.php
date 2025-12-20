<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByDomainRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

it('allows all emails when blocked domains list is empty', function () {
    Config::set('email-blocker.settings.blocked_domains', []);

    $rule = new BlockByDomainRule;

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('blocks emails with domains in blocked list and logs disabled', function () {
    Config::set('email-blocker.settings.blocked_domains', ['example.com']);
    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByDomainRule;

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe(['bob@test.com']);
});

it('blocks emails with domains in blocked list and logs enabled', function () {
    Config::set('email-blocker.settings.blocked_domains', ['example.com']);
    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByDomainRule;

    $context = (new Email)
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

it('blocks all emails if all domains are blocked', function () {
    Config::set('email-blocker.settings.blocked_domains', ['example.com', 'test.com']);
    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByDomainRule;

    $context = (new Email)
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
    $rule = new BlockByDomainRule;

    expect($rule->getReason())->toBe('Recipient email domain is blocked by configuration.');
});

it('blocks emails regardless of domain case', function () {
    Config::set('email-blocker.settings.blocked_domains', ['EXAMPLE.COM']);
    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByDomainRule;

    $emails = [
        'alice@example.com',
        'bob@EXAMPLE.com',
        'charlie@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe(['charlie@test.com']);
});
