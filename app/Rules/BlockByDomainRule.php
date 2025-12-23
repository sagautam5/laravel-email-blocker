<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;

class BlockByDomainRule extends BaseRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array
    {
        $domains = $this->domains();

        if (count($domains) === 0) {
            return $next($emails);
        }

        [$filtered, $blocked] = $this->filterEmails($domains, $emails);

        if (count($blocked) > 0) {
            $this->handleLog($blocked);
        }

        return $next($filtered);
    }

    public function getReason(): string
    {
        return 'Recipient email domain is blocked by configuration.';
    }

    /**
     * @return array<string>
     */
    public function domains(): array
    {
        $domains = config('email-blocker.settings.blocked_domains');

        if (empty($domains)) {
            return [];
        }

        $domains = array_map('strtolower', $domains);

        return array_flip($domains);
    }

    /**
     * @param  array<string>  $domains
     * @param  array<string>  $emails
     * 
     * @return array<array<string>>
     */
    protected function filterEmails($domains, $emails): array
    {
        $filtered = array_values(array_filter($emails, fn ($email) => ! isset($domains[substr(strrchr(strtolower($email), '@') ?: '', 1)])));

        return [$filtered, array_diff($emails, $filtered)];
    }
}
