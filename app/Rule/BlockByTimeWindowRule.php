<?php 

namespace Sagautam5\LaravelEmailBlocker\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\LaravelEmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\Supports\EmailContext;

class BlockByTimeWindowRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure
    {
        $from = config('email-blocker.time_window.from');
        $to   = config('email-blocker.time_window.to');
        $timezone = config('email-blocker.time_window.timezone', 'UTC');

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