<?php 

namespace Sagautam5\LaravelEmailBlocker\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\LaravelEmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\Supports\EmailContext;

class BlockByMailableRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure
    {
        $blockedMailables = config('email-blocker.settings.block_mailables', []);

        if (in_array(get_class($context->mailable), $blockedMailables)) {
            throw new EmailBlockedException('Blocked by MailableSpecificRule');
        }

        return $next($context);
    }
}
