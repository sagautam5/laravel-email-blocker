<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\TopBlockedRecipientMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by recipient email', function () {
    BlockedEmail::factory()->create([
        'email' => 'user1@example.com',
    ]);

    BlockedEmail::factory()->create([
        'email' => 'user1@example.com',
    ]);

    BlockedEmail::factory()->create([
        'email' => 'user2@example.com',
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'email' => 'user1@example.com',
        'total' => 2,
    ]);

    expect($result[1])->toMatchArray([
        'email' => 'user2@example.com',
        'total' => 1,
    ]);
});

it('orders results by total in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'email' => 'high@example.com',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'email' => 'low@example.com',
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate();

    expect($result[0]['email'])->toBe('high@example.com')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['email'])->toBe('low@example.com')
        ->and($result[1]['total'])->toBe(1);
});

it('respects the default limit of 10', function () {
    BlockedEmail::factory()->count(15)->sequence(
        fn ($sequence) => ['email' => "user{$sequence->index}@example.com"]
    )->create();

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(10);
});

it('respects custom limit when provided', function () {
    BlockedEmail::factory()->count(5)->sequence(
        fn ($sequence) => ['email' => "user{$sequence->index}@example.com"]
    )->create();

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate([
        'limit' => 3,
    ]);

    expect($result)->toHaveCount(3);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'email' => 'old@example.com',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['email'])->toBe('recent@example.com');
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'email' => 'old@example.com',
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['email'])->toBe('old@example.com');
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'email' => 'old@example.com',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'email' => 'in-range@example.com',
        'blocked_at' => now()->subDays(4),
    ]);

    BlockedEmail::factory()->create([
        'email' => 'recent@example.com',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date'   => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['email'])->toBe('in-range@example.com');
});

it('does not double-apply date filters', function () {
    BlockedEmail::factory()->count(2)->create([
        'email' => 'same@example.com',
        'blocked_at' => now()->subDays(3),
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
        'end_date'   => now()->subDays(1)->toDateTimeString(),
    ]);
    
    expect($result)->toHaveCount(1)
        ->and($result[0]['total'])->toBe(2);
});

it('always returns a stable response structure', function () {
    BlockedEmail::factory()->create([
        'email' => 'user@example.com',
    ]);

    $metric = new TopBlockedRecipientMetric();

    $result = $metric->calculate();

    expect($result[0])->toHaveKeys(['email', 'total']);
});
