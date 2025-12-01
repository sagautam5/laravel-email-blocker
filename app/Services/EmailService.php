<?php

namespace Sagautam5\LaravelEmailBlocker\App\Services;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

abstract class EmailService
{
    public function __construct(protected Email $message) {}
    /**
     * Get email sender.
     */
    protected function getFromEmail(): string
    {
        return once(fn () => $this->message->getFrom()[0]->getAddress());
    }

    /**
     * Get email subject.
     */
    protected function getSubject(): string
    {
        return once(fn () => (string) $this->message->getSubject());
    }

    /**
     * Get email body hash.
     */
    protected function getBodyHash(): string
    {
        return once(fn () => hash('sha256', strip_tags($this->getBody())));
    }

    /**
     * Get email body as string.
     */
    protected function getBody(): string
    {
        return once(fn () => (string) ($this->message->getHtmlBody() ?: $this->message->getTextBody()));
    }

    /**
     * Get all email addresses from the message.
     *
     * @return array<string>
     */
    protected function getAllRecipients(): array
    {
        return once(fn () => array_merge($this->getToEmails(), $this->getCcEmails(), $this->getBccEmails()));
    }

    /**
     * Get "To" email addresses from the message.
     *
     * @return array<string>
     */
    protected function getToEmails(): array
    {
        return once(function () {
            $addresses = $this->message->getTo();

            return $this->extractAddresses($addresses);
        });
    }

    /**
     * Get "Cc" email addresses from the message.
     *
     * @return array<string>
     */
    protected function getCcEmails(): array
    {
        return once(function () {
            $addresses = $this->message->getCc();

            return $this->extractAddresses($addresses);
        });
    }

    /**
     * Get "Bcc" email addresses from the message.
     *
     * @return array<string>
     */
    protected function getBccEmails(): array
    {
        return once(function () {
            $addresses = $this->message->getBcc();

            return $this->extractAddresses($addresses);
        });
    }

    /**
     * Convert Address objects into plain emails.
     *
     * @param  array<int, Address>|null  $addresses
     * @return array<string>
     */
    protected function extractAddresses(?array $addresses): array
    {
        return collect($addresses ?? [])
            ->map(fn (Address $a) => $a->getAddress())
            ->values()
            ->all();
    }
}
