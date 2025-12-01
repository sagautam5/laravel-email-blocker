<?php 

namespace Sagautam5\LaravelEmailBlocker\App\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\App\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\App\Supports\EmailContext;

class GlobalBlockRule
{
    public function handle(EmailContext $context, Closure $next)
    {
        if (config('email-blocker.global_block') === true) {
            throw new EmailBlockedException('Blocked by GlobalBlockRule');
        }

        return $next($context);
    }
}