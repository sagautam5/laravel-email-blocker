<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\BlockedOverTimeMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by date', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(1),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toHaveKeys(['date', 'total']);
    expect($result[1])->toHaveKeys(['date', 'total']);
});

it('aggregates multiple blocked emails on the same day', function () {
    BlockedEmail::factory()->count(3)->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate();

    expect($result)->toHaveCount(1)
        ->and($result[0]['total'])->toBe(3);
});

it('orders results by date ascending', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate();

    expect($result[0]['date'])->toBe(
        now()->subDays(3)->toDateString()
    );

    expect($result[1]['date'])->toBe(
        now()->subDay()->toDateString()
    );
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['date'])->toBe(
            now()->subDays(2)->toDateString()
        );
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['date'])->toBe(
            now()->subDays(5)->toDateString()
        );
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

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['date'])->toBe(
            now()->subDays(4)->toDateString()
        );
});

it('handles multiple days within date range correctly', function () {
    BlockedEmail::factory()->count(2)->create([
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->count(1)->create([
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(4)->toDateTimeString(),
        'end_date' => now()->subDays(1)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(2)
        ->and($result[0]['total'])->toBe(2)
        ->and($result[1]['total'])->toBe(1);
});

it('respects manual limit when applied', function () {
    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->create([
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedOverTimeMetric;

    $result = collect($metric->calculate())
        ->take(2)
        ->toArray();

    expect($result)->toHaveCount(2);
});
