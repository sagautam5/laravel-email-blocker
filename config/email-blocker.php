<?php

use Sagautam5\EmailBlocker\Rules\BlockByDomainRule;
use Sagautam5\EmailBlocker\Rules\BlockByEmailRule;
use Sagautam5\EmailBlocker\Rules\BlockByEnvironmentRule;
use Sagautam5\EmailBlocker\Rules\BlockByGlobalRule;
use Sagautam5\EmailBlocker\Rules\BlockByMailableRule;
use Sagautam5\EmailBlocker\Rules\BlockByTimeWindowRule;

return [

    'block_enabled' => env('EMAIL_BLOCK_ENABLED', true),
    'log_enabled' => false,

    'log_table' => 'blocked_emails',

    'rules' => [
        BlockByGlobalRule::class,
        BlockByEnvironmentRule::class,
        BlockByDomainRule::class,
        BlockByMailableRule::class,
        BlockByTimeWindowRule::class,
        BlockByEmailRule::class,

        // You can add your custom rule
        // App\Rules\CustomEmailBlockRule::class,
    ],

    'settings' => [
        'blocked_environments' => [
            // 'local',
            // 'staging',
        ],
        'time_window' => [
            'from' => null, // '09:00',
            'to' => null, // '18:00',
            'timezone' => null, // 'Asia/Kathmandu',
        ],
        'global_block' => true,
        'blocked_domains' => [
            // 'example.com',
            // E.g. 'gmail.com',

        ],
        'blocked_mailables' => [
            // SendWelcomeEmail::class,
            // E.g. 'App\Mail\WelcomeMail',
        ],
        'blocked_emails' => [
            // E.g. 'user@ample.com',
        ],
    ],
];
