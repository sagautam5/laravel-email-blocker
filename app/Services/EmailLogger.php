<?php 

namespace Sagautam5\EmailBlocker\Services;

use Sagautam5\EmailBlocker\Supports\EmailContext;

class EmailLogger
{
    public function info(EmailContext $context, string $class)
    {
        if(config('email-blocker.log_enabled') !== true) { return; }

        logger('Email blocked due to ' . $class . '!');
        // TODO: implement logging
    }
}