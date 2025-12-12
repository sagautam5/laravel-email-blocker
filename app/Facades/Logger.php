<?php

namespace Sagautam5\EmailBlocker\Facades;

use Illuminate\Support\Facades\Facade;
use Sagautam5\EmailBlocker\Services\EmailLogger;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;

/**
 * @method static void info(BlockedEmailContext $context)
 *
 * @see \Sagautam5\EmailBlocker\Services\EmailLogger
 */
class Logger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EmailLogger::class;
    }
}
