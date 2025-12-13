<?php

use App\Mail\SendWelcomeEmail;
use Sagautam5\EmailBlocker\Rules\BlockByDomainRule;
use Sagautam5\EmailBlocker\Rules\BlockByEnvironmentRule;
use Sagautam5\EmailBlocker\Rules\BlockByMailableRule;
use Sagautam5\EmailBlocker\Rules\BlockByTimeWindowRule;
use Sagautam5\EmailBlocker\Rules\BlockGloballlyRule;

return [

    'log_enabled' => true,

    'log_table' => 'blocked_emails',

    'rules' => [
        BlockGloballlyRule::class,
        BlockByEnvironmentRule::class,
        BlockByDomainRule::class,
        // BlockByMailableRule::class,
        BlockByTimeWindowRule::class,

        // User can add their own
        // App\Rules\CustomEmailBlockRule::class,
    ],

    'settings' => [
        'blocked_environments' => [
            // 'local',
            'staging',
        ],
        'time_window' => [
            'from' => '09:00',
            'to' => '18:00',
            'timezone' => 'Asia/Kathmandu',
        ],
        'global_block' => false,
        'blocked_domains' => [
            // 'example.com',
            // E.g. 'gmail.com',

        ],
        'blocked_mailables' => [
            // SendWelcomeEmail::class,
            // E.g. 'App\Mail\WelcomeMail',
        ],
    ],
];
