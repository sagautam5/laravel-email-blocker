<?php

namespace Sagautam5\EmailBlocker\Exceptions;

use RuntimeException;

final class InvalidConfigurationException extends RuntimeException
{
    public const INVALID_BOOLEAN = 1001;

    public const INVALID_RULE = 1002;

    public const INVALID_TIME_WINDOW = 1003;

    public const INVALID_DOMAIN = 1004;

    public const INVALID_MAILABLE = 1005;

    public const INVALID_EMAIL = 1006;

    public const INVALID_ENVIRONMENT = 1007;
}
