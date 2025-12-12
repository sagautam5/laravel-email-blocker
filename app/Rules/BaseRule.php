<?php

namespace Sagautam5\EmailBlocker\Rules;

use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;

abstract class BaseRule implements BlockEmailRule
{
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
