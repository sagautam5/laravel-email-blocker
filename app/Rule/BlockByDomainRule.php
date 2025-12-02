<?php 

namespace Sagautam5\LaravelEmailBlocker\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\LaravelEmailBlocker\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\Supports\EmailContext;

class BlockByDomainRule implements BlockEmailRule
{
    public function handle(EmailContext $context, Closure $next): Closure
    {
        $blockedDomains = config('email-blocker.settings.block_domains', []);

        foreach ($context->recipients as $recipient) {
            foreach ($blockedDomains as $domain) {

                // Regex pattern
                if (str_starts_with($domain, '/') && preg_match($domain, $recipient)) {
                    throw new EmailBlockedException('Blocked by DomainBlockRule');
                }

                // Ends-with pattern
                if (str_ends_with($recipient, $domain)) {
                    throw new EmailBlockedException('Blocked by DomainBlockRule');
                }
            }
        }

        return $next($context);
    }
}
