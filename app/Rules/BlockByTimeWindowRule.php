<?php

namespace Sagautam5\EmailBlocker\Rules;

use Carbon\Carbon;
use Closure;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;

class BlockByTimeWindowRule extends BaseRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array
    {
        $timeWindow = config('email-blocker.settings.time_window', []);

        if (! empty($timeWindow['from']) && ! empty($timeWindow['to'])) {
            $timezone = $timeWindow['timezone'] ?? config('app.timezone');
            $time = Carbon::now()->timezone($timezone);

            $from = Carbon::parse($timeWindow['from'], $timezone);
            $to = Carbon::parse($timeWindow['to'], $timezone);

            if ($from->gt($to)) {
                $to->addDay();
            }

            if ($time->between($from, $to)) {
                $this->handleLog($emails);

                return [];
            }
        }

        return $next($emails);
    }

    public function getReason(): string
    {
        return sprintf(
            'Email sending is blocked outside the allowed time window (%s–%s %s).',
            config('email-blocker.settings.time_window.from'),
            config('email-blocker.settings.time_window.to'),
            config('email-blocker.settings.time_window.timezone')
        );
    }
}
