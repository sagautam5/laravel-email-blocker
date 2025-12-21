<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\TopMailableRulePairsMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by mailable and rule pair', function () {
    BlockedEmail::factory()->count(2)->create([
        'mailable' => 'App\Mail\TestMail',
        'rule'     => 'SpamRule',
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\OtherMail',
        'rule'     => 'SpamRule',
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'mailable' => 'App\Mail\TestMail',
        'rule'     => 'SpamRule',
        'count'    => 2,
    ]);

    expect($result[1])->toMatchArray([
        'mailable' => 'App\Mail\OtherMail',
        'rule'     => 'SpamRule',
        'count'    => 1,
    ]);
});

it('orders results by count in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'mailable' => 'App\Mail\HighVolume',
        'rule'     => 'RuleA',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'mailable' => 'App\Mail\LowVolume',
        'rule'     => 'RuleA',
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result[0]['mailable'])->toBe('App\Mail\HighVolume')
        ->and($result[0]['count'])->toBe(3);

    expect($result[1]['mailable'])->toBe('App\Mail\LowVolume')
        ->and($result[1]['count'])->toBe(1);
});

it('coalesces null mailable and rule values to Unknown', function () {
    BlockedEmail::factory()->count(2)->create([
        'mailable' => null,
        'rule'     => null,
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(1)
        ->and($result[0])->toMatchArray([
            'mailable' => 'Unknown',
            'rule'     => 'Unknown',
            'count'    => 2,
        ]);
});

it('handles mixed null and valid mailable-rule combinations', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\TestMail',
        'rule'     => null,
    ]);

    BlockedEmail::factory()->create([
        'mailable' => null,
        'rule'     => 'SpamRule',
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result)->toContainEqual([
        'mailable' => 'App\Mail\TestMail',
        'rule'     => 'Unknown',
        'count'    => 1,
    ]);

    expect($result)->toContainEqual([
        'mailable' => 'Unknown',
        'rule'     => 'SpamRule',
        'count'    => 1,
    ]);
});

it('respects the default limit of 10', function () {
    BlockedEmail::factory()->count(15)->sequence(
        fn ($sequence) => [
            'mailable' => "App\Mail\Mail{$sequence->index}",
            'rule'     => "Rule{$sequence->index}",
        ]
    )->create();

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result)->toHaveCount(10);
});

it('respects custom limit when provided', function () {
    BlockedEmail::factory()->count(5)->sequence(
        fn ($sequence) => [
            'mailable' => "App\Mail\Mail{$sequence->index}",
            'rule'     => "Rule{$sequence->index}",
        ]
    )->create();

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate([
        'limit' => 3,
    ]);

    expect($result)->toHaveCount(3);
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\OldMail',
        'rule'       => 'OldRule',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\RecentMail',
        'rule'       => 'RecentRule',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\RecentMail');
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\OldMail',
        'rule'       => 'OldRule',
        'blocked_at' => now()->subDays(5),
    ]);

    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\RecentMail',
        'rule'       => 'RecentRule',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate([
        'end_date' => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\OldMail');
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\TooOld',
        'rule'       => 'Rule1',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\InRange',
        'rule'       => 'Rule2',
        'blocked_at' => now()->subDays(4),
    ]);

    BlockedEmail::factory()->create([
        'mailable'   => 'App\Mail\TooRecent',
        'rule'       => 'Rule3',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate([
        'start_date' => now()->subDays(6)->toDateTimeString(),
        'end_date'   => now()->subDays(2)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0])->toMatchArray([
            'mailable' => 'App\Mail\InRange',
            'rule'     => 'Rule2',
            'count'    => 1,
        ]);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('always returns a stable response structure', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\TestMail',
        'rule'     => 'SpamRule',
    ]);

    $metric = new TopMailableRulePairsMetric();

    $result = $metric->calculate();

    expect($result[0])->toHaveKeys([
        'mailable',
        'rule',
        'count',
    ]);
});
