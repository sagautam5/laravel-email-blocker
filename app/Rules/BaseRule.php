<?php

namespace Sagautam5\EmailBlocker\Rules;

use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Events\EmailBlockedEvent;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;
use Sagautam5\EmailBlocker\Supports\EmailContext;

abstract class BaseRule implements BlockEmailRule
{
    public ?EmailContext $context = null;

    public ?ReceiverType $type = null;

    public function setContext(EmailContext $context, ReceiverType $type): void
    {
        $this->context = $context;
        $this->type = $type;
    }

    abstract public function getReason(): string;

    protected function handleLog(array $emails)
    {
        if (config('email-blocker.log_enabled') !== true) {
            return;
        }

        foreach ($emails as $email) {
            $blockedContext = new BlockedEmailContext(
                email: $email,
                reason: $this->getReason(),
                context: $this->context,
                rule: get_class($this),
                receiver_type: $this->type,
            );
            event(new EmailBlockedEvent($blockedContext));
        }
    }
}
