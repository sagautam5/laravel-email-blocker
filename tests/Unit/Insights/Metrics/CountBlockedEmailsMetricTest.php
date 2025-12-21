<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\CountBlockedEmailsMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns total count of blocked emails', function () {
    BlockedEmail::factory()->count(3)->create();

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate();

    expect($result)
        ->toBeArray()
        ->toHaveKey('count')
        ->and($result['count'])->toBe(3);
});

it('returns zero when no blocked emails exist', function () {
    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate();

    expect($result)
        ->toBeArray()
        ->and($result['count'])->toBe(0);
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result['count'])->toBe(1);
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result['count'])->toBe(1);
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(4),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date'   => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result['count'])->toBe(1);
});

it('includes records exactly on start_date boundary', function () {
    $boundary = now()->subDays(3);

    BlockedEmail::factory()->create([
        'blocked_at' => $boundary,
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate([
        'start_date' => $boundary->toDateTimeString(),
    ]);

    expect($result['count'])->toBe(1);
});

it('includes records exactly on end_date boundary', function () {
    $boundary = now()->subDays(3);

    BlockedEmail::factory()->create([
        'blocked_at' => $boundary,
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate([
        'end_date' => $boundary->toDateTimeString(),
    ]);

    expect($result['count'])->toBe(1);
});

it('ignores unrelated columns when counting', function () {
    BlockedEmail::factory()->count(2)->create([
        'rule' => null,
        'mailable' => null,
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'SomeRule',
        'mailable' => 'App\\Mail\\TestMail',
    ]);

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate();

    expect($result['count'])->toBe(3);
});

it('always returns a stable response shape', function () {
    BlockedEmail::factory()->count(1)->create();

    $metric = new CountBlockedEmailsMetric();

    $result = $metric->calculate();

    expect(array_keys($result))->toBe(['count']);
});
