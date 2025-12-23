<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;

class BlockByEnvironmentRule extends BaseRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array
    {
        $blockedEnvironments = config('email-blocker.settings.blocked_environments', []);

        if (count($blockedEnvironments) > 0) {
            if (in_array(app()->environment(), $blockedEnvironments)) {
                $this->handleLog($emails);

                return [];
            }
        }

        return $next($emails);
    }

    public function getReason(): string
    {
        return sprintf(
            'Email sending is blocked in the "%s" environment.',
            app()->environment()
        );
    }
}
