## Laravel Email Blocker

![Build](https://github.com/sagautam5/laravel-email-blocker/workflows/CI/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/sagautam5/laravel-email-blocker/v)](//packagist.org/packages/sagautam5/laravel-email-blocker)
[![Total Downloads](https://poser.pugx.org/sagautam5/laravel-email-blocker/downloads)](//packagist.org/packages/sagautam5/laravel-email-blocker)
[![Issues](https://img.shields.io/github/issues/sagautam5/laravel-email-blocker
)](https://github.com/sagautam5/laravel-email-blocker/issues) [![Stars](https://img.shields.io/github/stars/sagautam5/laravel-email-blocker
)](https://github.com/sagautam5/laravel-email-blocker/stargazers) 
[![License](https://img.shields.io/github/license/sagautam5/laravel-email-blocker)](https://github.com/sagautam5/laravel-email-blocker/blob/master/LICENSE) 
[![Forks](https://img.shields.io/github/forks/sagautam5/laravel-email-blocker
)](https://github.com/sagautam5/laravel-email-blocker/network/members) 
[![Twitter](https://img.shields.io/twitter/url?url=https%3A%2F%2Fgithub.com%2Fsagautam5%2Flaravel-email-blocker
)](https://twitter.com/intent/tweet?text=Wow:&url=https%3A%2F%2Fgithub.com%2Fsagautam5%2Flaravel-email-blocker)


## 🚀 Introduction

Laravel Email Blocker is a lightweight and extensible package that allows you to control, block, log, and analyze outgoing emails using configurable, rule-based logic.

It is especially useful for:

- 🧪 Local, testing, and staging environments
- 🏢 Multi-tenant or enterprise systems
- 📜 Compliance-sensitive applications
- 🚨 Preventing accidental emails to real users

The package integrates seamlessly with Laravel’s mail system and introduces minimal overhead.

## 📑 Table of Contents

- [Introduction](#-introduction)
- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
    - [Default Configuration](#default-configuration)
    - [Validate Configuration](#validate-configuration)
- [Usage Guide](#-usage-guide)
    - [Disable Email Blocking Completely](#disable-email-blocking-completely)
    - [Disable Specific Rules](#disable-specific-rules)
    - [Built-in Blocking Rules](#-built-in-blocking-rules)
        - [Global Block](#1️⃣-global-block)
        - [Environment Block](#2️⃣-environment-block)
        - [Domain Block](#3️⃣-domain-block)
        - [Mailable Block](#4️⃣-mailable-block)
        - [Time Window Block](#5️⃣-time-window-block)
        - [Email Block](#6️⃣-email-block)
- [Insights & Metrics](#-insights--metrics)
    - [Available Metrics](#available-metrics)
    - [Common Filters](#common-filters)
    - [Example Usage](#example-usage)
    - [Example Output](#example-output)
- [Customization](#-customization)
    - [Example Custom Rule](#example-custom-rule)
    - [Example Custom Metric](#example-custom-metric)
        - [Usage](#usage)
        - [Output](#output)
- [Security](#-security)
- [Contributing](#-contributing)
- [Credits](#-credits)
- [Support](#-support)
- [License](#-license)

---

## ✨ Features
- 🚫 Rule-based email blocking
- 🧩 Pluggable & extensible rule architecture
- 📝 Persistent logging of blocked emails
- 📊 Built-in insights & metrics
- 🧪 Pest PHP–friendly test setup
- ⚙️ Zero changes required to existing mail code

---

## 📋 Requirements

- PHP **8.2** or higher
- Laravel **11.x** or higher

## 📦 Installation

Install via Composer:

```bash
composer require sagautam5/laravel-email-blocker
``` 
The package supports Laravel auto-discovery.

## 🔧 Configuration

Publish Configuration File
```bash 
php artisan vendor:publish --provider="Sagautam5\EmailBlocker\EmailBlockerServiceProvider --tag="config"
```

This will create:
```php
config/email-blocker.php
```

### Default Configuration

```php
<?php

use Sagautam5\EmailBlocker\Rules\BlockByDomainRule;
use Sagautam5\EmailBlocker\Rules\BlockByEmailRule;
use Sagautam5\EmailBlocker\Rules\BlockByEnvironmentRule;
use Sagautam5\EmailBlocker\Rules\BlockByGlobalRule;
use Sagautam5\EmailBlocker\Rules\BlockByMailableRule;
use Sagautam5\EmailBlocker\Rules\BlockByTimeWindowRule;

return [

    // Master switch for the package
    'block_enabled' => env('EMAIL_BLOCK_ENABLED', true),

    // Enable database logging of blocked emails
    'log_enabled' => env('EMAIL_BLOCK_LOG_ENABLED', false),

    // Database table for logs
    'log_table' => 'blocked_emails',

    // Applied rules (executed in order)
    'rules' => [
        BlockByGlobalRule::class,
        BlockByEnvironmentRule::class,
        BlockByDomainRule::class,
        BlockByMailableRule::class,
        BlockByTimeWindowRule::class,
        BlockByEmailRule::class,

        // App\Rules\CustomEmailBlockRule::class,
    ],

    // Rule-specific settings
    'settings' => [

        'global_block' => env('GLOBAL_EMAIL_BLOCK_ENABLED', false),

        'blocked_environments' => [
            // 'local',
            // 'staging',
        ],

        'blocked_domains' => [
            // 'example.com',
        ],

        'blocked_mailables' => [
            // App\Mail\WelcomeMail::class,
        ],

        'blocked_emails' => [
            // 'user@example.com',
        ],

        'time_window' => [
            'from' => null, // '09:00'
            'to' => null,   // '18:00'
            'timezone' => null, // 'Asia/Kathmandu'
        ],
    ],
];
```

### Validate Configuration
After executing `vendor:publish` to publish configuration file, you can adjust things as well as add new rules. To validate that your configuration is valid, you can just run following console command:

```sh
php artisan email-blocker:validate
```

If correct then,

```sh
✔ Email Blocker configuration is valid.
```

otherwise, you could see something like this in console:

```sh 
✖ Email Blocker configuration is invalid.

email-blocker: 'settings.blocked_environments' must be an array of strings.

```
## 🧪 Usage Guide
### Disable Email Blocking Completely

To disable email blocking entirely, set the following environment variable to false in your .env file:
```php
EMAIL_BLOCK_ENABLED=false
```
This disables all rules and logging. By default, email block is enabled.

### Disable Specific Rules

Simply remove the rule class from the rules array in `config/email-blocker.php`.

### 🧱 Built-in Blocking Rules
#### 1️⃣ Global Block

Blocks all outgoing emails.
```php
GLOBAL_EMAIL_BLOCK_ENABLED=true
```

or

```php
'global_block' => true,
```

By default, it's not enabled.

#### 2️⃣ Environment Block
Blocks emails in selected environments.

```php
'blocked_environments' => [
    'local',
    'testing',
],
```
By default, emails are not blocked in any environments.

#### 3️⃣ Domain Block

Blocks emails sent to specific domains.
```php
'blocked_domains' => [
    'example.com',
],
```

By default, emails are not blocked in any domains

#### 4️⃣ Mailable Block
Blocks email sent via specific `Mailable` classes.

```php
'blocked_mailables' => [
    'App\Mail\WelcomeMail',
],
```

By default, emails sent via all mailable classes are not blocked.

#### 5️⃣ Time Window Block
Blocks email addresses during a defined time range.
```php
'time_window' => [
    'from' => '09:00',
    'to' => '18:00',
    'timezone' => 'Asia/Kathmandu',
],
```

- Uses 24-hour format
- Timezone-aware ( By default, `config('app.timezone')` value is used)
#### 6️⃣ Email Block
Blocks specific recipient email addresses.
```php
'blocked_emails' => [
    'user@ample.com',
],
```
By default, no email addresses are blocked.

## 📊 Insights & Metrics

When logging is enabled, the package provides ready-to-use metrics to analyze email blocking behavior.

### Available Metrics

| Metric Class                     | Description                            |
| -------------------------------- | -------------------------------------- |
| `CountBlockedEmailsMetric`       | Total number of blocked emails         |
| `BlockedByRuleMetric`            | Emails blocked per rule                |
| `BlockedByMailableMetric`        | Emails blocked per mailable            |
| `BlockedOverTimeMetric`          | Blocking trends over time              |
| `ReceiverTypeDistributionMetric` | Distribution by receiver type          |
| `TopBlockedRecipientMetric`      | Most frequently blocked recipients     |
| `TopBlockedSenderMetric`         | Senders triggering most blocks         |
| `TopMailableRulePairsMetric`     | Most common mailable–rule combinations |

---
### Common Filters
Most metrics support:
```php 
[
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-24',
    'limit' => 5,
]
```

### Example Usage
```php
use Sagautam5\EmailBlocker\Insights\Metrics\BlockedByMailableMetric;

$metric = new BlockedByMailableMetric();

$result = $metric->calculate([
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-24',
    'limit' => 5,
]);
```

### Example Output
```php
[
    [
        'mailable' => 'App\Mail\WelcomeMail',
        'total' => 10,
    ],
    [
        'mailable' => 'App\Mail\OrderConfirmationMail',
        'total' => 9,
    ],
]
```

## 🧩 Customization

You can create custom blocking rules or custom metrics by extending the provided base classes:

- `BaseRule`

- `BaseMetric`

This allows deep customization without modifying the core package.

### Example Custom Rule

```php

namespace App\Custom\Rules;

use Closure;
use App\Models\BlackList;
use Sagautam5\EmailBlocker\Abstracts\BaseRule;

class BlockEmailsInBlackListRule extends BaseRule
{
    /**
     * @param  array<string>  $emails
     * @return Closure|array<string>
     */
    public function handle(array $emails, Closure $next): Closure|array
    {
        $blockedEmails = BlackList::whereIn('emails', $emails)->pluck('emails')->toArray();
        
        if (!empty($blockedEmails)) {
            $this->handleLog($blockedEmails);

            return [];
        }

        return $next($emails);
    }

    public function getReason(): string
    {
        return 'Email is present on black list.';
    }
}

```

To enable this block rule, we need to add this into rules array inside config file

```php

use App\Custom\Rules\BlockEmailsInBlackListRule;

'rules' => [
    // Other Builtin Rules

    BlockEmailsInBlackListRule::class,
],

```

After this, all emails will be checked by this rule.

### Example Custom Metric
```php

namespace App\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Sagautam5\EmailBlocker\Abstracts\BaseMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;
use Sagautam5\EmailBlocker\Enums\ReceiverType;

class TotalBlockedPrimaryEmails extends BaseMetric
{
    public function getName(): string
    {
        return 'Total Blocked Primary Emails';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        /**
         * @var Builder<BlockedEmail> $query
         */
        $query = BlockedEmail::query()->where('receiver_type', ReceiverType::CC);

        $query = $this->applyDateFilters($query, $filters);

        return [
            'count' => $query->count(),
        ];
    }
}
```

##### Usage

```php
use App\Insights\Metrics\TotalBlockedPrimaryEmails;

$metric = new TotalBlockedPrimaryEmails();

$totalBlocked = $metric->calculate([
    'start_date' => '2025-06-01',
    'end_date' => '2025-12-31'
]);
```

##### Output

```php
[ 
    'count' => 10
];
```

## Testing
```bash
composer test
```

## 🔐 Security
Please review [our security policy](SECURITY) on how to report security vulnerabilities.


## 🤝 Contributing
Please see [CONTRIBUTING](CONTRIBUTING) for details.

## ⚖️ Code of Conduct
In order to ensure that the this community is welcoming to all, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT).


## 👥 Credits

- [Sagar Gautam](https://github.com/sagautam5) — Author & Maintainer
- All Contributors

## ⭐ Support

If this package helps you:

- ⭐ Star the repository
- 🐛 Report issues
- 💡 Suggest improvements

Your support is appreciated !


## 📄 License

This package is open-sourced software licensed under the MIT license.

See the full license here:
[LICENSE](LICENSE)