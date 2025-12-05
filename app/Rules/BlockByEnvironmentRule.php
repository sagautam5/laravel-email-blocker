<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Supports\EmailContext;

class BlockByEnvironmentRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure|bool
    {
        $blockedEnvironments = config('email-blocker.settings.blocked_environments', []);

        if (count($blockedEnvironments) > 0) {
            if (in_array(app()->environment(), $blockedEnvironments)) {
                event(new EmailBlockedEvent($context, self::class));
                return false;
            }
        }

        return $next($context);
    }
}
