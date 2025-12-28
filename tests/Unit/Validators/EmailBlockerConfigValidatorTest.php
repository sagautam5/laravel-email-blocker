<?php

use Sagautam5\EmailBlocker\Validators\EmailBlockerConfigValidator;
use Sagautam5\EmailBlocker\Exceptions\InvalidConfigurationException;

uses()->group('validator');

beforeEach(function () {
    // minimal valid config
    $this->validConfig = [
        'block_enabled' => true,
        'log_enabled' => false,
        'rules' => [],
        'settings' => [
            'global_block' => true,
            'time_window' => [
                'from' => null,
                'to' => null,
                'timezone' => null,
            ],
            'blocked_environments' => [],
            'blocked_domains' => [],
            'blocked_mailables' => [],
            'blocked_emails' => [],
        ],
    ];
});

it('passes with valid configuration', function () {
    expect(fn () => EmailBlockerConfigValidator::validate($this->validConfig))->not()->toThrow(InvalidConfigurationException::class);
});

it('fails when booleans are invalid', function () {
    $this->validConfig['block_enabled'] = 'yes';

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'block_enabled' must be a boolean.");
});

it('fails when settings.global_block is invalid', function () {
    $this->validConfig['settings']['global_block'] = 'nope';

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'settings.global_block' must be a boolean.");
});

it('fails when a rule class does not exist', function () {
    $this->validConfig['rules'] = ['NonExistentRule'];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: Rule class 'NonExistentRule' does not exist.");
});

it('fails when a rule does not implement BlockEmailRule', function () {
    // Create a dummy class that exists but does not implement the interface
    eval('class DummyRule {}');

    $this->validConfig['rules'] = [DummyRule::class];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: '".DummyRule::class."' must implement EmailBlockRule.");
});

it('fails for invalid time window', function () {
    $this->validConfig['settings']['time_window']['from'] = '25:00';

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: time_window.from must be a valid 24-hour time.");
});

it('fails for invalid timezone', function () {
    $this->validConfig['settings']['time_window']['timezone'] = 'Invalid/Timezone';

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: Invalid timezone 'Invalid/Timezone'.");
});

it('fails for invalid environment', function () {
    $this->validConfig['settings']['blocked_environments'] = [null];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'settings.blocked_environments' must be an array of strings.");
});

it('fails for invalid domain', function () {
    $this->validConfig['settings']['blocked_domains'] = ['invalid@domain.com'];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'invalid@domain.com' is not a valid domain.");
});

it('fails for invalid mailable', function () {
    // Class does not exist
    $this->validConfig['settings']['blocked_mailables'] = ['NonExistentMailable'];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'NonExistentMailable' must be a valid Mailable class.");
});

it('fails for invalid email', function () {
    $this->validConfig['settings']['blocked_emails'] = ['not-an-email'];

    expect(fn () =>
        EmailBlockerConfigValidator::validate($this->validConfig)
    )->toThrow(InvalidConfigurationException::class, "email-blocker: 'not-an-email' is not a valid email address.");
});
