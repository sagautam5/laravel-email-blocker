<?php

use Sagautam5\LaravelEmailBlocker\Rule\BlockByDomainRule;
use Sagautam5\LaravelEmailBlocker\Rule\BlockByEnvironmentRule;
use Sagautam5\LaravelEmailBlocker\Rule\BlockByMailableRule;
use Sagautam5\LaravelEmailBlocker\Rule\BlockByTimeWindowRule;
use Sagautam5\LaravelEmailBlocker\Rule\BlockGloballlyRule;

return [

    'log_table' => 'blocked_emails',

    'rules' => [
        BlockGloballlyRule::class,
        BlockByDomainRule::class,
        BlockByEnvironmentRule::class,
        BlockByMailableRule::class,
        BlockByTimeWindowRule::class,
        
        // User can add their own
        // App\Rules\CustomEmailBlockRule::class,
    ],

    'settings' => [
        'blocked_environments' => [
            'local', 
            'staging'
        ],
        'time_window' => [
            'from' => '09:00',
            'to'   => '18:00',
            'timezone' => 'Asia/Kathmandu',
        ],
        'global_block' => false,
        'blocked_domains' => [
            // E.g. 'gmail.com',
            
        ],
        'blocked_mailables' => [
            // E.g. 'App\Mail\WelcomeMail',
        ],
    ],
];
