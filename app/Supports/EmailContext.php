<?php

namespace Sagautam5\EmailBlocker\Supports;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailContext
{
    public function __construct(public Email $message) {}

    /**
     * Get email sender.
     */
    public function getFromEmail(): string
    {
        return once(fn () => $this->message->getFrom()[0]->getAddress());
    }

    /**
     * Get email subject.
     */
    public function getSubject(): string
    {
        return once(fn () => (string) $this->message->getSubject());
    }

    /**
     * Get email body as string.
     */
    public function getBody(): string
    {
        return once(fn () => (string) ($this->message->getHtmlBody() ?: $this->message->getTextBody()));
    }

    /**
     * Get "To" email addresses from the message.
     *
     * @return array<string>
     */
    public function getToEmails(): array
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
    public function getCcEmails(): array
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
    public function getBccEmails(): array
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
