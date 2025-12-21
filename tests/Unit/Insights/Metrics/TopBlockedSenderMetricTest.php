<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\TopBlockedSenderMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by sender email', function () {
    BlockedEmail::factory()->create([
        'from_email' => 'sender1@example.com',
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'sender1@example.com',
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'sender2@example.com',
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'from_email' => 'sender1@example.com',
        'total' => 2,
    ]);

    expect($result[1])->toMatchArray([
        'from_email' => 'sender2@example.com',
        'total' => 1,
    ]);
});

it('orders results by total in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'from_email' => 'high@example.com',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'from_email' => 'low@example.com',
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate();

    expect($result[0]['from_email'])->toBe('high@example.com')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['from_email'])->toBe('low@example.com')
        ->and($result[1]['total'])->toBe(1);
});

it('respects the default limit of 10', function () {
    BlockedEmail::factory()->count(15)->sequence(
        fn ($sequence) => ['from_email' => "sender{$sequence->index}@example.com"]
    )->create();

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(10);
});

it('respects custom limit when provided', function () {
    BlockedEmail::factory()->count(5)->sequence(
        fn ($sequence) => ['from_email' => "sender{$sequence->index}@example.com"]
    )->create();

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate([
        'limit' => 3,
    ]);

    expect($result)->toHaveCount(3);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'from_email' => 'old@example.com',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['from_email'])->toBe('recent@example.com');
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'from_email' => 'old@example.com',
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['from_email'])->toBe('old@example.com');
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'from_email' => 'old@example.com',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'in-range@example.com',
        'blocked_at' => now()->subDays(4),
    ]);

    BlockedEmail::factory()->create([
        'from_email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date'   => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['from_email'])->toBe('in-range@example.com');
});

it('always returns a stable response structure', function () {
    BlockedEmail::factory()->create([
        'from_email' => 'sender@example.com',
    ]);

    $metric = new TopBlockedSenderMetric();

    $result = $metric->calculate();

    expect($result[0])->toHaveKeys(['from_email', 'total']);
});
