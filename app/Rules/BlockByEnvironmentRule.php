<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockByEnvironmentRule extends BaseRule
{
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
        return 'Evironment Block on '.app()->environment();
    }
}
