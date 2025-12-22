# Laravel Email Blocker

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

## 📊 Available Metrics

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


## 👥 Contributors

Sagar Gautam — Author & Maintainer

## 📄 License

This package is open-sourced software licensed under the MIT license.

See the full license here:
[LICENSE](github.com/sagautam5/laravel-email-blocker)

## ⭐ Support

If this package helps you:

- ⭐ Star the repository
- 🐛 Report issues
- 💡 Suggest improvements

Your support is appreciated!