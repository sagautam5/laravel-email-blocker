<?php 

namespace Sagautam5\EmailBlocker\Facades;

use Illuminate\Support\Facades\Facade;
use Sagautam5\EmailBlocker\Services\EmailLogger;
use Sagautam5\EmailBlocker\Supports\EmailContext;

/**
 * @method static void info(EmailContext $context, string $class)
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