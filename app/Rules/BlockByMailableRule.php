<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;

class BlockByMailableRule implements BlockEmailRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        return $next($emails);
    }
}
