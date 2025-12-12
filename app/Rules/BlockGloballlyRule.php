<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;

class BlockGloballlyRule implements BlockEmailRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        if (config('email-blocker.settings.global_block') === true) {
            return [];
        }

        return $next($emails);
    }
}
