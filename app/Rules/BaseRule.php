<?php

namespace Sagautam5\EmailBlocker\Rules;

use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;
use Sagautam5\EmailBlocker\Supports\EmailContext;

abstract class BaseRule implements BlockEmailRule
{
    protected EmailContext $context;

    public function setContext(EmailContext $context): void
    {
        $this->context = $context;
    }

    abstract public function getReason(): string;

    protected function handleLog(array $emails)
    {
        if (config('email-blocker.log_enabled') !== true) {
            return;
        }

        foreach ($emails as $email) {
            event(new EmailBlockedEvent(new BlockedEmailContext($email, $this->getReason())));
        }
    }
}
