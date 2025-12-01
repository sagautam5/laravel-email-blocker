<?php 

namespace Sagautam5\LaravelEmailBlocker\App\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\App\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\App\Supports\EmailContext;

class TimeWindowRule
{
    public function handle(EmailContext $context, Closure $next)
    {
        [$from, $to] = config('email-blocker.block_time_window', [null, null]);
        $timezone = config('email-blocker.timezone', 'UTC');

        if ($from && $to) {
            $now = now()->setTimezone($timezone);
            $fromTime = $now->copy()->setTimeFromTimeString($from);
            $toTime   = $now->copy()->setTimeFromTimeString($to);

            $inBlockedRange = $from < $to
                ? $now->between($fromTime, $toTime)
                : ($now->greaterThan($fromTime) || $now->lessThan($toTime));

            if ($inBlockedRange) {
                throw new EmailBlockedException('Blocked by TimeWindowRule');
            }
        }

        return $next($context);
    }
}