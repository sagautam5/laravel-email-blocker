<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Rules\BlockByTimeWindowRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Email;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Carbon::setTestNow(Carbon::create(2025, 1, 1, 10, 0, 0, 'UTC'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('allows all emails when time window is not configured', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => null,
        'to' => null,
        'timezone' => null,
    ]);

    $rule = new BlockByTimeWindowRule;

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('allows emails when current time is outside the blocked time window', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => '12:00',
        'to' => '18:00',
        'timezone' => 'UTC',
    ]);

    $rule = new BlockByTimeWindowRule;

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe($emails);
});

it('blocks all emails when current time is within the blocked time window and logging disabled', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => '09:00',
        'to' => '11:00',
        'timezone' => 'UTC',
    ]);

    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByTimeWindowRule;

    $emails = [
        'alice@example.com',
        'bob@test.com',
    ];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('blocks all emails when current time is within the blocked time window and logs enabled', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => '09:00',
        'to' => '11:00',
        'timezone' => 'UTC',
    ]);

    Config::set('email-blocker.log_enabled', true);

    $rule = new BlockByTimeWindowRule;

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

    assertDatabaseHas('blocked_emails', [
        'email' => 'alice@example.com',
    ]);

    assertDatabaseHas('blocked_emails', [
        'email' => 'bob@test.com',
    ]);
});

it('respects configured timezone when evaluating time window', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => '15:00',
        'to' => '16:00',
        'timezone' => 'Asia/Kathmandu',
    ]);

    Config::set('email-blocker.log_enabled', false);

    $rule = new BlockByTimeWindowRule;

    $emails = ['alice@example.com'];

    $result = $rule->handle($emails, nextClosure());

    expect($result)->toBe([]);
});

it('returns correct block reason', function () {
    Config::set('email-blocker.settings.time_window', [
        'from' => '09:00',
        'to' => '18:00',
        'timezone' => 'UTC',
    ]);

    $rule = new BlockByTimeWindowRule;

    expect($rule->getReason())->toBe(
        'Email sending is blocked outside the allowed time window (09:00–18:00 UTC).'
    );
});
