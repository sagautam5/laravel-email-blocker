<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockByMailableRule extends BaseRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        return $next($emails);
    }

    public function getReason(): string
    {
        return 'Mailable Block';
    }
}
