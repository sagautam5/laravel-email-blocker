<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\ReceiverTypeDistributionMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by receiver_type', function () {
    BlockedEmail::factory()->create([
        'receiver_type' => 'to',
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'to',
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'cc',
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'receiver_type' => 'to',
        'total' => 2,
    ]);

    expect($result[1])->toMatchArray([
        'receiver_type' => 'cc',
        'total' => 1,
    ]);
});

it('orders results by total in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'receiver_type' => 'bcc',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'receiver_type' => 'to',
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate();

    expect($result[0]['receiver_type'])->toBe('bcc')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['receiver_type'])->toBe('to')
        ->and($result[1]['total'])->toBe(1);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'receiver_type' => 'to',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'cc',
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['receiver_type'])->toBe('cc')
        ->and($result[0]['total'])->toBe(1);
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'receiver_type' => 'to',
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'cc',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['receiver_type'])->toBe('to')
        ->and($result[0]['total'])->toBe(1);
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'receiver_type' => 'to',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'cc',
        'blocked_at' => now()->subDays(4),
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'bcc',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date'   => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['receiver_type'])->toBe('cc')
        ->and($result[0]['total'])->toBe(1);
});

it('aggregates totals correctly for multiple receiver types in range', function () {
    BlockedEmail::factory()->count(3)->create([
        'receiver_type' => 'to',
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->count(2)->create([
        'receiver_type' => 'cc',
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(3)->toDateTimeString(),
        'end_date'   => now()->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(2)
        ->and($result[0]['receiver_type'])->toBe('to')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['receiver_type'])->toBe('cc')
        ->and($result[1]['total'])->toBe(2);
});

it('respects manual limit when applied', function () {
    BlockedEmail::factory()->count(3)->create([
        'receiver_type' => 'to',
    ]);

    BlockedEmail::factory()->count(2)->create([
        'receiver_type' => 'cc',
    ]);

    BlockedEmail::factory()->create([
        'receiver_type' => 'bcc',
    ]);

    $metric = new ReceiverTypeDistributionMetric();

    $result = collect($metric->calculate())
        ->take(2)
        ->toArray();

    expect($result)->toHaveCount(2)
        ->and($result[0]['receiver_type'])->toBe('to')
        ->and($result[1]['receiver_type'])->toBe('cc');
});
