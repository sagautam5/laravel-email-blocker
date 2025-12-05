<?php 

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\EmailBlocker\Supports\EmailContext;

class BlockByMailableRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure|bool
    {
        $blockedMailables = config('email-blocker.settings.block_mailables', []);

        if (in_array(get_class($context->mailable), $blockedMailables)) {
            throw new EmailBlockedException('Blocked by MailableSpecificRule');
        }

        return $next($context);
    }
}
