<?php

namespace Sagautam5\EmailBlocker\Supports;

use Sagautam5\EmailBlocker\Enums\ReceiverType;

class BlockedEmailContext
{
    public function __construct(
        public string $email,
        public string $reason,
        public string $rule,
        public EmailContext $context,
        public ReceiverType $receiver_type
    ) {}
}
