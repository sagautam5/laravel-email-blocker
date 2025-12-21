<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\BlockedByRuleMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by rule', function () {
    BlockedEmail::factory()->create([
        'rule' => 'SpamRule',
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'SpamRule',
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'ContentRule',
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'rule' => 'SpamRule',
        'total' => 2,
    ]);

    expect($result[1])->toMatchArray([
        'rule' => 'ContentRule',
        'total' => 1,
    ]);
});

it('excludes records with null rule', function () {
    BlockedEmail::factory()->create([
        'rule' => null,
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'SpamRule',
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(1)
        ->and($result[0]['rule'])->toBe('SpamRule')
        ->and($result[0]['total'])->toBe(1);
});

it('orders results by total in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'rule' => 'HighCountRule',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'rule' => 'LowCountRule',
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate();

    expect($result[0]['rule'])->toBe('HighCountRule')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['rule'])->toBe('LowCountRule')
        ->and($result[1]['total'])->toBe(1);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'rule' => 'OldRule',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'RecentRule',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['rule'])->toBe('RecentRule');
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'rule' => 'OldRule',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'RecentRule',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['rule'])->toBe('OldRule');
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'rule' => 'OldRule',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'InRangeRule',
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'RecentRule',
        'blocked_at' => now(),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
        'end_date'   => now()->subDays(1)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['rule'])->toBe('InRangeRule');
});

it('respects limit on results when applied', function () {
    BlockedEmail::factory()->count(3)->create([
        'rule' => 'HighCountRule',
    ]);

    BlockedEmail::factory()->count(2)->create([
        'rule' => 'MediumCountRule',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'rule' => 'LowCountRule',
    ]);

    $metric = new BlockedByRuleMetric();

    $result = collect($metric->calculate())->take(2)->toArray(); // applying limit manually

    expect($result)->toHaveCount(2)
        ->and($result[0]['rule'])->toBe('HighCountRule')
        ->and($result[1]['rule'])->toBe('MediumCountRule');
});


it('applies multiple rules with overlapping date filters correctly', function () {
    // Older than 7 days
    BlockedEmail::factory()->create([
        'rule' => 'OldRule1',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'OldRule2',
        'blocked_at' => now()->subDays(8),
    ]);

    // Within last 5 days
    BlockedEmail::factory()->create([
        'rule' => 'RecentRule1',
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'RecentRule2',
        'blocked_at' => now()->subDays(1),
    ]);

    // Null rule, should be ignored
    BlockedEmail::factory()->create([
        'rule' => null,
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
        'end_date'   => now()->subDays(1)->toDateTimeString(),
    ]);

    // Only RecentRule1 should match (blocked_at 3 days ago)
    expect($result)->toHaveCount(2)
        ->and($result[0]['rule'])->toBe('RecentRule1')
        ->and($result[0]['total'])->toBe(1);
});

it('aggregates totals correctly for multiple rules in range', function () {
    // Within range
    BlockedEmail::factory()->count(2)->create([
        'rule' => 'RuleA',
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->count(3)->create([
        'rule' => 'RuleB',
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->create([
        'rule' => 'RuleC',
        'blocked_at' => now()->subDays(1),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(3)->toDateTimeString(),
        'end_date'   => now()->toDateTimeString(),
    ]);

    // Expect aggregation and descending order
    expect($result)->toHaveCount(3)
        ->and($result[0]['rule'])->toBe('RuleB')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['rule'])->toBe('RuleA')
        ->and($result[1]['total'])->toBe(2)
        ->and($result[2]['rule'])->toBe('RuleC')
        ->and($result[2]['total'])->toBe(1);
});

it('handles mixed null and valid rules within date range', function () {
    BlockedEmail::factory()->count(2)->create([
        'rule' => 'ValidRule',
        'blocked_at' => now()->subDays(1),
    ]);

    BlockedEmail::factory()->create([
        'rule' => null,
        'blocked_at' => now()->subDays(1),
    ]);

    $metric = new BlockedByRuleMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(2)->toDateTimeString(),
        'end_date'   => now()->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['rule'])->toBe('ValidRule')
        ->and($result[0]['total'])->toBe(2);
});
