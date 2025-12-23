<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;

class BlockByEmailRule extends BaseRule implements BlockEmailRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array
    {
        [$filtered, $blocked] = $this->filterEmails($emails);

        if (count($blocked) > 0) {
            $this->handleLog($blocked);
        }

        return $next($filtered);
    }

    public function getReason(): string
    {
        return 'Sender email address is blocked by configuration.';
    }

    /**
     * @return array<string>
     */
    public function getBlockedEmails(): array
    {
        return config('email-blocker.settings.blocked_emails', []);
    }

    /**
     * @param  array<string>  $emails
     * @return array<array<string>>
     */
    protected function filterEmails(array $emails): array
    {
        $filtered = array_values(array_filter($emails, fn ($email) => ! in_array($email, $this->getBlockedEmails())));

        return [$filtered, array_diff($emails, $filtered)];
    }
}
