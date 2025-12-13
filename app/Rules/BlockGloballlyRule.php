<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockGloballlyRule extends BaseRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        if (config('email-blocker.settings.global_block') == true) {
            $this->handleLog($emails);

            return [];
        }

        return $next($emails);
    }

    public function getReason(): string
    {
        return 'Global Block';
    }
}
