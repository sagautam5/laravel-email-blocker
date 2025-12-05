<?php 

namespace Sagautam5\EmailBlocker\Contracts;

use Closure;
use Sagautam5\EmailBlocker\Supports\EmailContext;

interface BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure|bool;
}
