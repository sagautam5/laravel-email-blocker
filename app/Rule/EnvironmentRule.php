<?php

namespace Sagautam5\LaravelEmailBlocker\App\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\App\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\App\Supports\EmailContext;

class EnvironmentRule
{
    public function handle(EmailContext $context, Closure $next)
    {
        if (config('email-blocker.block_in_environments', [])) {
            if (in_array(app()->environment(), config('email-blocker.block_in_environments'))) {
                throw new EmailBlockedException('Blocked by EnvironmentRule');
            }
        }

        return $next($context);
    }
}
