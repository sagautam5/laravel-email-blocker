<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByGlobalRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

it('blocks all emails when global block is enabled and logs are disabled', function () {
    Config::set('email-blocker.settings.global_block', true);
    Config::set('email-blocker.log_enabled', false);
    $rule = new BlockByGlobalRule;

    $emails = [
        'test1@example.com',
        'test2@example.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('blocks all emails when global block is enabled and logs are enabled', function () {
    Config::set('email-blocker.settings.global_block', true);
    Config::set('email-blocker.log_enabled', true);
    $rule = new BlockByGlobalRule;

    $context = (new Email)
        ->from('test1@example.com')
        ->to('test1@example.com')
        ->subject('Test subject')
        ->html('<p>Hello</p>');

    $rule->setContext(new EmailContext($context), ReceiverType::TO);

    $result = $rule->handle(['test1@example.com'], nextClosure());

    expect($result)->toBe([]);

    assertDatabaseHas('blocked_emails', [
        'email' => 'test1@example.com',
        'rule' => BlockByGlobalRule::class,
        'receiver_type' => ReceiverType::TO->value,
    ]);
});

it('allows emails to pass when global block is disabled', function () {
    Config::set('email-blocker.settings.global_block', false);

    $rule = new BlockByGlobalRule;

    $emails = ['allowed@example.com'];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('returns correct block reason', function () {
    $rule = new BlockByGlobalRule;

    expect($rule->getReason())
        ->toBe('Email sending is globally disabled by configuration.');
});
