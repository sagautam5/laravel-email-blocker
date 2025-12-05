<?php 

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\EmailBlocker\Supports\EmailContext;

class BlockByTimeWindowRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure|bool
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