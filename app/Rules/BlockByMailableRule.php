<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockByMailableRule extends BaseRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        $mailables = $this->mailables();

        if (count($mailables) === 0) {
            return $next($emails);
        }

        [$filtered, $blocked] = $this->filterEmails($mailables, $emails);

        if (count($blocked) > 0) {
            $this->handleLog($blocked);
        }

        return $next($filtered);
    }

    public function getReason(): string
    {
        return 'Mailable Block';
    }

    protected function mailables(): array
    {
        $mailables = config('email-blocker.settings.blocked_mailables');

        if (empty($mailables)) {
            return [];
        }

        return array_flip($mailables);
    }

    protected function filterEmails(array $mailables, array $emails): array
    {
        $filtered = array_values(array_filter($emails, fn ($email) => ! isset($mailables[$email['mailable'] ?? null])
        ));

        return [$filtered, array_diff($emails, $filtered)];
    }
}
