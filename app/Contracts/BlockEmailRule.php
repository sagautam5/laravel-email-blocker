<?php

namespace Sagautam5\EmailBlocker\Contracts;

use Closure;

interface BlockEmailRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array;

    public function getReason(): string;
}
