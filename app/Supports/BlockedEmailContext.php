<?php

namespace Sagautam5\EmailBlocker\Supports;

class BlockedEmailContext
{
    public function __construct(
        public string $email,
        public string $reason
    ) {}
}
