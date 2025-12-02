<?php 

namespace Sagautam5\LaravelEmailBlocker\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\LaravelEmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\Supports\EmailContext;

class BlockGloballlyRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure
    {
        if (config('email-blocker.settings.global_block') === true) {
            throw new EmailBlockedException('Blocked by GlobalBlockRule');
        }

        return $next($context);
    }
}