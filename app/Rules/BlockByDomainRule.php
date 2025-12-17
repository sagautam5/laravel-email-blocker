<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockByDomainRule extends BaseRule
{
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

    public function domains(): array
    {
        $domains = config('email-blocker.settings.blocked_domains');

        if (empty($domains)) {
            return [];
        }

        $domains = array_map('strtolower', $domains);

        return array_flip($domains);
    }

    protected function filterEmails($domains, $emails)
    {
        $filtered = array_values(array_filter($emails, fn ($email) => ! isset($domains[substr(strrchr(strtolower($email), '@'), 1)])));

        return [$filtered, array_diff($emails, $filtered)];
    }
}
