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

Laravel Email Blocker is a lightweight, extensible package that allows you to **block outgoing emails based on custom rules**, **log blocked emails**, and **analyze email-blocking behavior using insightful metrics**.

It is ideal for staging environments, QA systems, multi-tenant applications, and compliance-sensitive projects where controlling outgoing emails is critical.

---


## ✨ Features

- 🚫 Block outgoing emails using rule-based logic
- 🧩 Easily extendable rule architecture
- 📝 Persist blocked emails for auditing
- 📊 Built-in insights & metrics
- 🧪 Pest PHP–friendly test setup
- ⚙️ Minimal impact on existing mail flow

---

## 📦 Installation

Install the package via Composer:

```bash
composer require sagautam5/laravel-email-blocker
``` 

## 🔧 Configuration

### Applied Rules

| Rule                     | Purpose                            |
| ------------------------ | ---------------------------------- |
| `BlockByGlobalRule`      | Emergency stop – blocks all emails |
| `BlockByEnvironmentRule` | Blocks based on app environment    |
| `BlockByDomainRule`      | Blocks domain specific emails      |
| `BlockByMailableRule`    | Blocks specific mailable classes   |
| `BlockByTimeWindowRule`  | Restricts emails within time frame |
| `BlockByEmailRule`       | Blocks exact email addresses       |

### Customization
For customization, you can publish configuration file and make adjustments as per your requirements.

To publish the package configuration file, run:

```sh
php artisan vendor:publish --provider="Sagautam5\EmailBlocker\EmailBlockerServiceProvider" --tag="config"
```

This will create the following file:
```php
config/email-blocker.php
```
## 🧪 Usage Guide
### Disabling Email Blocking

To disable email blocking entirely, set the following environment variable to false in your .env file:
```php
EMAIL_BLOCK_ENABLED=false
```

### Disable Existing Rules

To disable existing rules, just remove rule from list of rules array in the config file.

### Rule Based Configuration
Currently, a set of general-purpose rules is included in the default setup. These rules can be enabled or disabled as needed, and the package also provides options for customization.
#### Global Block

This rule can be applied to disable all emails sent from the system. To enable it, simply set this variable to true.
```php
GLOBAL_EMAIL_BLOCK_ENABLED=true
```

or

```php
'global_block' => true,
```

By default, it is set to false.

#### Environment Block
This rule blocks emails in specific environments (e.g., local, staging). Add environments in your configurations.

```php
'blocked_environments' => [
    'local',
    'testing',
],
```
By default, emails are not blocked in any environments.

#### Domain Block

This rule blocks emails sent to specific domains. Add domains in your configuration:
```php
'blocked_domains' => [
    'example.com',
],
```

By default, emails are not blocked in any domains

#### Mailable Block
This rule blocks specific mailables. Add mailable class names in your configuration:
```php
'blocked_mailables' => [
    'App\Mail\WelcomeMail',
],
```

By default, emails are not blocked for any mailable

#### Time Window Block
This rule blocks emails during a specific time window within given timezone. Configure start and end times:

```php
'time_window' => [
    'from' => '09:00',
    'to' => '18:00',
    'timezone' => 'Asia/Kathmandu',
],
```

Hour should be in 24 hours format. By default, emails are not blocked for a time range. 

#### Email Block
This rule blocks specific email addresses. Add emails in your configuration:
```php
'blocked_emails' => [
    'user@ample.com',
],
```
By default, no individual emails are blocked.

## 📊 Available Insights

The package includes several built-in metrics for analyzing blocked emails:

- BlockedByMailableMetric
- BlockedByRuleMetric
- BlockedOverTimeMetric
- CountBlockedEmailsMetric
- ReceiverTypeDistributionMetric
- TopBlockedRecipientMetric
- TopBlockedSenderMetric
- TopMailableRulePairsMetric

These help identify:

- Frequently blocked mailables
- Over-aggressive rules
- Blocking trends over time


### 📊 Insights Overview
| Metric Class                  | What It Represents                                                      | Filter Options                         |
| ----------------------------- | ----------------------------------------------------------------------- | ------------------------------------- |
| `BlockedByMailableMetric`      | Shows how many emails were blocked **for each mail class**.            | `start_date`, `end_date`, `limit`     |
| `BlockedByRuleMetric`          | Shows how many emails were blocked **by each blocking rule**.          | `start_date`, `end_date`, `limit`     |
| `BlockedOverTimeMetric`        | Shows trends of blocked emails **over time** (per day/week/month).     | `start_date`, `end_date`  |
| `CountBlockedEmailsMetric`     | Total number of blocked emails in a given time period.                 | `start_date`, `end_date`               |
| `ReceiverTypeDistributionMetric` | Shows distribution of blocked emails **by receiver type**.          | `start_date`, `end_date`               |
| `TopBlockedRecipientMetric`    | Lists recipients who **had the most blocked emails**.                  | `start_date`, `end_date`, `limit`     |
| `TopBlockedSenderMetric`       | Lists senders who **triggered the most blocked emails**.               | `start_date`, `Hour should be in 24 hours formatend_date`, `limit`     |
| `TopMailableRulePairsMetric`   | Shows **which mailables are blocked by which rules most frequently**.  | `start_date`, `end_date`, `limit`     |

### Basic Usage

#### Filters
```php

$filters = [
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-24',
    'limit' => 5, // return top 5 blocked mailables
];

$metric = new BlockedByMailableMetric();
$metric->calculate($filters)
```

#### Result
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

## 👥 Contributors

Sagar Gautam — Author & Maintainer

## 📄 License

This package is open-sourced software licensed under the MIT license.

See the full license here:
[LICENSE](github.com/sagautam5/laravel-email-blocker/LICENSE)

## ⭐ Support

If this package helps you:

- ⭐ Star the repository
- 🐛 Report issues
- 💡 Suggest improvements

Your support is appreciated !