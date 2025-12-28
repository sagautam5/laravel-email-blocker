<?php 

namespace Sagautam5\EmailBlocker\Validators;

use Illuminate\Mail\Mailable;
use Sagautam5\EmailBlocker\Exceptions\InvalidConfigurationException;
use Sagautam5\EmailBlocker\Contracts\BlockEmailRule;

class EmailBlockerConfigValidator
{
    public static function validate(array $config)
    {
        self::validateBooleans($config);
        self::validateRules($config['rules']);
        self::validateEnvironments($config['settings']['blocked_environments']);
        self::validateTimeWindow($config['settings']['time_window']);
        self::validateDomains($config['settings']['blocked_domains']);
        self::validateMailables($config['settings']['blocked_mailables']);
        self::validateEmails($config['settings']['blocked_emails']);
    }

    private static function validateEnvironments(array $environments): void
    {
        foreach ($environments as $environment) {
            if (! is_string($environment)) {
                throw new InvalidConfigurationException(
                    "email-blocker: 'settings.blocked_environments' must be an array of strings.",
                    InvalidConfigurationException::INVALID_ENVIRONMENT
                );
            }
        }
    }

    private static function validateBooleans(array $config): void
    {
        foreach (['block_enabled', 'log_enabled'] as $key) {
            if (! is_bool($config[$key])) {
                throw new InvalidConfigurationException(
                    "email-blocker: '{$key}' must be a boolean.",
                    InvalidConfigurationException::INVALID_BOOLEAN
                );
            }
        }

        if (! is_bool($config['settings']['global_block'] ?? null)) {
            throw new InvalidConfigurationException(
                "email-blocker: 'settings.global_block' must be a boolean.",
                InvalidConfigurationException::INVALID_BOOLEAN
            );
        }
    }

    private static function validateRules(array $rules): void
    {
        foreach ($rules as $rule) {
            if (! class_exists($rule)) {
                throw new InvalidConfigurationException(
                    "email-blocker: Rule class '{$rule}' does not exist.",
                    InvalidConfigurationException::INVALID_RULE
                );
            }

            if (! is_subclass_of($rule, BlockEmailRule::class)) {
                throw new InvalidConfigurationException(
                    "email-blocker: '{$rule}' must implement EmailBlockRule.",
                    InvalidConfigurationException::INVALID_RULE
                );
            }
        }
    }

    private static function validateTimeWindow(array $window): void
    {
        foreach (['from', 'to'] as $key) {
            if (! empty($window[$key])) {
                if (! preg_match('/^\d{2}:\d{2}$/', $window[$key])) {
                    throw new InvalidConfigurationException(
                        "email-blocker: time_window.{$key} must be in HH:MM format.",
                        InvalidConfigurationException::INVALID_TIME_WINDOW
                    );
                }

                [$hours, $minutes] = explode(':', $window[$key]);

                if ((int)$hours > 23 || (int)$minutes > 59) {
                    throw new InvalidConfigurationException(
                        "email-blocker: time_window.{$key} must be a valid 24-hour time.",
                        InvalidConfigurationException::INVALID_TIME_WINDOW
                    );
                }
            }
        }

        if (! empty($window['timezone']) && ! in_array($window['timezone'], timezone_identifiers_list(), true)) {
            throw new InvalidConfigurationException(
                "email-blocker: Invalid timezone '{$window['timezone']}'.",
                InvalidConfigurationException::INVALID_TIME_WINDOW
            );
        }
    }


    private static function validateDomains(array $domains): void
    {
        foreach ($domains as $domain) {
            if (! is_string($domain) || str_contains($domain, '@')) {
                throw new InvalidConfigurationException(
                    "email-blocker: '{$domain}' is not a valid domain.",
                    InvalidConfigurationException::INVALID_DOMAIN
                );
            }
        }
    }

    private static function validateMailables(array $mailables): void
    {
        foreach ($mailables as $mailable) {
            if (! class_exists($mailable) || ! is_subclass_of($mailable, Mailable::class)) {
                throw new InvalidConfigurationException(
                    "email-blocker: '{$mailable}' must be a valid Mailable class.",
                    InvalidConfigurationException::INVALID_MAILABLE
                );
            }
        }
    }

    private static function validateEmails(array $emails): void
    {
        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidConfigurationException(
                    "email-blocker: '{$email}' is not a valid email address.",
                    InvalidConfigurationException::INVALID_EMAIL
                );
            }
        }
    }
}