<?php

use Sagautam5\EmailBlocker\Exceptions\InvalidConfigurationException;

it('has correct constants', function () {
    expect(InvalidConfigurationException::INVALID_BOOLEAN)->toBe(1001);
    expect(InvalidConfigurationException::INVALID_RULE)->toBe(1002);
    expect(InvalidConfigurationException::INVALID_TIME_WINDOW)->toBe(1003);
    expect(InvalidConfigurationException::INVALID_DOMAIN)->toBe(1004);
    expect(InvalidConfigurationException::INVALID_MAILABLE)->toBe(1005);
    expect(InvalidConfigurationException::INVALID_EMAIL)->toBe(1006);
    expect(InvalidConfigurationException::INVALID_ENVIRONMENT)->toBe(1007);
});
