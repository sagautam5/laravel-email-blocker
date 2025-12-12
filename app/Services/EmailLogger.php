<?php

namespace Sagautam5\EmailBlocker\Services;

use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;

class EmailLogger
{
    public function info(BlockedEmailContext $context)
    {
        logger('Email blocked due to: '.$context->reason);
    }
}
