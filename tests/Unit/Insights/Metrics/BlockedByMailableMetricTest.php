<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sagautam5\EmailBlocker\Insights\Metrics\BlockedByMailableMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

uses(RefreshDatabase::class);

it('returns blocked emails grouped by mailable', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\TestMail',
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\TestMail',
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\AnotherMail',
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate();

    expect($result)->toHaveCount(2);

    expect($result[0])->toMatchArray([
        'mailable' => 'App\Mail\TestMail',
        'total' => 2,
    ]);

    expect($result[1])->toMatchArray([
        'mailable' => 'App\Mail\AnotherMail',
        'total' => 1,
    ]);
});

it('excludes records with null mailable', function () {
    BlockedEmail::factory()->create([
        'mailable' => null,
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\TestMail',
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate();

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\TestMail')
        ->and($result[0]['total'])->toBe(1);
});

it('orders results by total in descending order', function () {
    BlockedEmail::factory()->count(3)->create([
        'mailable' => 'App\Mail\HighCountMail',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'mailable' => 'App\Mail\LowCountMail',
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate();

    expect($result[0]['mailable'])->toBe('App\Mail\HighCountMail')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['mailable'])->toBe('App\Mail\LowCountMail')
        ->and($result[1]['total'])->toBe(1);
});

it('returns an empty array when no blocked emails exist', function () {
    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate();

    expect($result)->toBeArray()->toBeEmpty();
});

it('applies start_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\OldMail',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\RecentMail',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\RecentMail');
});

it('applies end_date filter correctly', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\OldMail',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\RecentMail',
        'blocked_at' => now()->subDay(),
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate([
        'end_date' => now()->subDays(5)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\OldMail');
});

it('applies both start_date and end_date filters correctly', function () {
    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\OldMail',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\InRangeMail',
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'App\Mail\RecentMail',
        'blocked_at' => now(),
    ]);

    $metric = new BlockedByMailableMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
        'end_date' => now()->subDays(1)->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('App\Mail\InRangeMail');
});

it('respects limit on results when applied', function () {
    // Assuming we want to limit the number of results manually
    BlockedEmail::factory()->count(3)->create([
        'mailable' => 'App\Mail\HighCountMail',
    ]);

    BlockedEmail::factory()->count(2)->create([
        'mailable' => 'App\Mail\MediumCountMail',
    ]);

    BlockedEmail::factory()->count(1)->create([
        'mailable' => 'App\Mail\LowCountMail',
    ]);

    $metric = new BlockedByMailableMetric;

    $result = collect($metric->calculate())->take(2)->toArray(); // applying limit manually

    expect($result)->toHaveCount(2)
        ->and($result[0]['mailable'])->toBe('App\Mail\HighCountMail')
        ->and($result[1]['mailable'])->toBe('App\Mail\MediumCountMail');
});

it('applies multiple mailables with overlapping date filters correctly', function () {
    // Older than 7 days
    BlockedEmail::factory()->create([
        'mailable' => 'OldMail1',
        'blocked_at' => now()->subDays(10),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'OldMail2',
        'blocked_at' => now()->subDays(8),
    ]);

    // Within last 5 days
    BlockedEmail::factory()->create([
        'mailable' => 'RecentMail1',
        'blocked_at' => now()->subDays(3),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'RecentMail2',
        'blocked_at' => now()->subDays(1),
    ]);

    // Null mailable, should be ignored
    BlockedEmail::factory()->create([
        'mailable' => null,
        'blocked_at' => now()->subDays(2),
    ]);

    $metric = new \Sagautam5\EmailBlocker\Insights\Metrics\BlockedByMailableMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(5)->toDateTimeString(),
        'end_date' => now()->subDays(1)->toDateTimeString(),
    ]);

    // Only RecentMail1 and RecentMail2 should match
    expect($result)->toHaveCount(2)
        ->and($result[0]['mailable'])->toBe('RecentMail1')
        ->and($result[1]['mailable'])->toBe('RecentMail2');
});

it('aggregates totals correctly for multiple mailables in range', function () {
    // Within range
    BlockedEmail::factory()->count(2)->create([
        'mailable' => 'MailA',
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->count(3)->create([
        'mailable' => 'MailB',
        'blocked_at' => now()->subDays(2),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => 'MailC',
        'blocked_at' => now()->subDays(1),
    ]);

    $metric = new \Sagautam5\EmailBlocker\Insights\Metrics\BlockedByMailableMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(3)->toDateTimeString(),
        'end_date' => now()->toDateTimeString(),
    ]);

    // Expect aggregation and descending order
    expect($result)->toHaveCount(3)
        ->and($result[0]['mailable'])->toBe('MailB')
        ->and($result[0]['total'])->toBe(3)
        ->and($result[1]['mailable'])->toBe('MailA')
        ->and($result[1]['total'])->toBe(2)
        ->and($result[2]['mailable'])->toBe('MailC')
        ->and($result[2]['total'])->toBe(1);
});

it('handles mixed null and valid mailables within date range', function () {
    BlockedEmail::factory()->count(2)->create([
        'mailable' => 'ValidMail',
        'blocked_at' => now()->subDays(1),
    ]);

    BlockedEmail::factory()->create([
        'mailable' => null,
        'blocked_at' => now()->subDays(1),
    ]);

    $metric = new \Sagautam5\EmailBlocker\Insights\Metrics\BlockedByMailableMetric;

    $result = $metric->calculate([
        'start_date' => now()->subDays(2)->toDateTimeString(),
        'end_date' => now()->toDateTimeString(),
    ]);

    expect($result)->toHaveCount(1)
        ->and($result[0]['mailable'])->toBe('ValidMail')
        ->and($result[0]['total'])->toBe(2);
});
