<?php

namespace Sagautam5\EmailBlocker\Contracts;

use Closure;

interface BlockEmailRule
{
    public function handle(array $emails, Closure $next): Closure|array;

    public function getReason(): string;
}
