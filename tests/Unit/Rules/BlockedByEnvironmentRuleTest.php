<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByEnvironmentRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

it('blocks all emails when current environment is blocked and logs are disabled', function () {
    Config::set('email-blocker.settings.blocked_environments', ['testing']);
    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByEnvironmentRule;

    $emails = [
        'test1@example.com',
        'test2@example.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('blocks all emails when current environment is blocked and logs are enabled', function () {
    Config::set('email-blocker.settings.blocked_environments', ['testing']);
    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByEnvironmentRule;

    $context = (new Email)
        ->from('system@example.com')
        ->to('test@example.com')
        ->subject('Test subject')
        ->html('<p>Hello</p>');

    $rule->setContext(
        new EmailContext($context),
        ReceiverType::TO
    );

    $result = $rule->handle(['test@example.com'], nextClosure());

    expect($result)->toBe([]);

    assertDatabaseHas('blocked_emails', [
        'email' => 'test@example.com',
        'rule' => BlockByEnvironmentRule::class,
        'receiver_type' => ReceiverType::TO->value,
    ]);
});

it('allows emails to pass when current environment is not blocked', function () {
    Config::set('email-blocker.settings.blocked_environments', ['production']);

    $rule = new BlockByEnvironmentRule;

    $emails = ['allowed@example.com'];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('allows emails to pass when blocked environments list is empty', function () {
    Config::set('email-blocker.settings.blocked_environments', []);

    $rule = new BlockByEnvironmentRule;

    $emails = ['allowed@example.com'];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('returns correct block reason with environment name', function () {
    $rule = new BlockByEnvironmentRule;

    expect($rule->getReason())
        ->toBe(
            sprintf(
                'Email sending is blocked in the "%s" environment.',
                app()->environment()
            )
        );
});
