<?php

namespace Sagautam5\EmailBlocker\Rules;

use Closure;

class BlockByMailableRule extends BaseRule
{
    public function handle(array $emails, Closure $next): Closure|array
    {
        $mailables = $this->mailables();

        if (in_array($this->context->mailable, $mailables)) {
            $this->handleLog($emails);

            return [];
        }

        return $next($emails);
    }

    public function getReason(): string
    {
        return sprintf(
            'The mailable "%s" is blocked from being sent.',
            class_basename($this->context->mailable)
        );
    }

    protected function mailables(): array
    {
        return config('email-blocker.settings.blocked_mailables');
    }
}
