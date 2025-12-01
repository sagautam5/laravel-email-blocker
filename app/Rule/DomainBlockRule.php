<?php 

namespace Sagautam5\LaravelEmailBlocker\App\Rule;

use Closure;
use Sagautam5\LaravelEmailBlocker\App\Exceptions\EmailBlockedException;
use Sagautam5\LaravelEmailBlocker\App\Supports\EmailContext;

class DomainBlockRule
{
    public function handle(EmailContext $context, Closure $next)
    {
        $blockedDomains = config('email-blocker.block_domains', []);

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
