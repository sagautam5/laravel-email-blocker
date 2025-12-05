<?php 

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Supports\EmailContext;

class BlockGloballlyRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure|bool
    {
        if (config('email-blocker.settings.global_block') === true) {
            event(new EmailBlockedEvent($context, self::class));
            return false;
        }

        return $next($context);
    }
}