<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;

class BlockByEnvironmentRule implements BlockEmailRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        $blockedEnvironments = config('email-blocker.settings.blocked_environments', []);

        if (count($blockedEnvironments) > 0) {
            if (in_array(app()->environment(), $blockedEnvironments)) {
                return [];
            }
        }

        return $next($emails);
    }
}
