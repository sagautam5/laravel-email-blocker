<?php 

namespace Sagautam5\LaravelEmailBlocker\App\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\App\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\App\Supports\EmailContext;

class MailableSpecificRule
{
    public function handle(EmailContext $context, Closure $next)
    {
        $blockedMailables = config('email-blocker.block_mailables', []);

        if (in_array(get_class($context->mailable), $blockedMailables)) {
            throw new EmailBlockedException('Blocked by MailableSpecificRule');
        }

        return $next($context);
    }
}
