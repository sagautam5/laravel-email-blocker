<?php 

namespace Sagautam5\LaravelEmailBlocker\Contracts;

use Closure;
use Sagautam5\LaravelEmailBlocker\Supports\EmailContext;

interface BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure;
}
