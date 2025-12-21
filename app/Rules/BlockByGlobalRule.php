<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;

class BlockByGlobalRule extends BaseRule
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
        return 'Email sending is globally disabled by configuration.';
    }
}
