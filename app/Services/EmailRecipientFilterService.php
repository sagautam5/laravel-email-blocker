<?php

namespace Sagautam5\LaravelEmailBlocker\Services;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailRecipientFilterService extends EmailService
{
    public function __construct(protected Email $message) {}

    /**
     * Prevent duplicate emails
     */
    public function filterReceivers(): Email|bool
    {
        [$to, $cc, $bcc] = $this->getFilteredReceivers();

        if (empty(array_filter([$to, $cc, $bcc]))) {
            return false;
        }

        $to = $this->ensureToExists($to, $cc, $bcc);

        $this->applyReceivers($to, $cc, $bcc);

        return $this->message;
    }

    /**
     * filter all allowed receivers.
     *
     * @return array{0: array<string>, 1: array<string>, 2: array<string>}
     */
    protected function getFilteredReceivers(): array
    {
        return array_map(
            fn ($emails) => $this->allowedEmails($emails),
            [
                $this->getToEmails(),
                $this->getCcEmails(),
                $this->getBccEmails(),
            ]
        );
    }

    /**
     * Ensure at least one "To" recipient exists.
     * Falls back to first Cc or Bcc if necessary.
     *
     * @param  array<string>  $to
     * @param  array<string>  $cc
     * @param  array<string>  $bcc
     * @return array<string>
     */
    protected function ensureToExists(array $to, array &$cc, array &$bcc): array
    {
        if (! empty($to)) {
            return $to;
        }

        if (! empty($cc)) {
            $to[] = array_shift($cc);
        }

        if (empty($to) && ! empty($bcc)) {
            $to[] = array_shift($bcc);
        }

        return $to;
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

    /**
     * Filter out receivers that have already been sent today.
     *
     * @param  array<string>  $emails
     * @return array<string>
     */
    protected function allowedEmails(array $emails): array
    {
        return $emails;
    }

    /**
     * Apply "To", "Cc", and "Bcc" receivers.
     *
     * @param  array<string>  $to
     * @param  array<string>  $cc
     * @param  array<string>  $bcc
     */
    protected function applyReceivers(array $to, array $cc, array $bcc): void
    {
        $this->applyTo($to);

        $this->applyCc($this->mapToAddresses($cc));
        $this->applyBcc($this->mapToAddresses($bcc));
    }

    /**
     * Convert plain emails into Address objects.
     *
     * @param  array<string>  $emails
     * @return array<int, Address>
     */
    protected function mapToAddresses(array $emails): array
    {
        return array_map(fn ($email) => new Address($email), $emails);
    }

    /**
     * Apply "To" receivers.
     *
     * @param  array<string>  $to
     */
    protected function applyTo(array $to): void
    {
        if (empty($to)) {
            throw new \RuntimeException('Cannot apply receivers: no "to" email address available.');
        }
        $this->message->to($to[0]);
    }

    /**
     * Apply "Cc" receivers.
     *
     * @param  array<int, Address>  $cc
     */
    protected function applyCc(array $cc): void
    {
        $this->removeCc();

        foreach ($cc as $ccAddress) {
            $this->message->addCc($ccAddress);
        }
    }

    /**
     * Remove all "Cc" receivers.
     */
    protected function removeCc(): void
    {
        $this->message->cc();
    }

    /**
     * Apply "Bcc" receivers.
     *
     * @param  array<int, Address>  $bcc
     */
    protected function applyBcc(array $bcc): void
    {
        $this->removeBcc();

        foreach ($bcc as $bccAddress) {
            $this->message->addBcc($bccAddress);
        }
    }

    /**
     * Remove all "Bcc" receivers.
     */
    protected function removeBcc(): void
    {
        $this->message->bcc();
    }
}
