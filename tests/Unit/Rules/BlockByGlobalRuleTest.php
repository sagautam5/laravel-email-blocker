<?php

use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Rules\BlockByGlobalRule;

function nextClosure(): Closure {
    return fn (array $emails) => $emails;
}

it('blocks all emails when global block is enabled', function () {
    Config::set('email-blocker.settings.global_block', true);
    Config::set('email-blocker.log_enabled', false);
    $rule = new BlockByGlobalRule();

    $emails = [
        'test1@example.com',
        'test2@example.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('allows emails to pass when global block is disabled', function () {
    Config::set('email-blocker.settings.global_block', false);

    $rule = new BlockByGlobalRule();

    $emails = ['allowed@example.com'];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('returns correct block reason', function () {
    $rule = new BlockByGlobalRule();

    expect($rule->getReason())
        ->toBe('Email sending is globally disabled by configuration.');
});
