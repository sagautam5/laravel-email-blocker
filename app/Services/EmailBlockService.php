<?php

namespace Sagautam5\EmailBlocker\Services;

use Illuminate\Pipeline\Pipeline;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailBlockService
{
    private EmailContext $context;

    public function __construct(protected Email $message)
    {
        $this->context = new EmailContext($this->message);
    }

    /**
     * Apply block rules to receivers.
     */
    public function applyRules(): Email|bool
    {
        $this->applyRulesOnTo();
        $this->applyRulesOnCc();
        $this->applyRulesOnBcc();

        if ($this->checkIfEmptyReceivers()) {
            return false;
        }

        return $this->message;
    }

    /**
     * Apply Rules on "To" receivers.
     */
    protected function applyRulesOnTo(): void
    {
        $receivers = $this->getFilteredReceivers($this->context->getToEmails());

        $this->applyTo($receivers);
    }

    /**
     * Apply Rules on "Cc" receivers.
     */
    protected function applyRulesOnCc(): void
    {
        $receivers = $this->getFilteredReceivers($this->context->getCcEmails());

        $this->applyCc($receivers);
    }

    /**
     * Apply Rules on "Bcc" receivers.
     */
    protected function applyRulesOnBcc(): void
    {
        $receivers = $this->getFilteredReceivers($this->context->getBccEmails());

        $this->applyBcc($receivers);
    }

    /**
     * Check if all receivers are empty.
     */
    protected function checkIfEmptyReceivers(): bool
    {
        return
            empty($this->context->getToEmails()) &&
            empty($this->context->getCcEmails()) &&
            empty($this->context->getBccEmails());
    }

    /**
     * @param  array<string>  $emails
     */
    protected function getFilteredReceivers($emails): array
    {
        return app(Pipeline::class)
            ->send($emails)
            ->through(config('email-blocker.rules'))
            ->then(function (array $emails) {
                return true;
            });
    }

    /**
     * Apply "To" receivers.
     *
     * @param  array<string>  $to
     */
    protected function applyTo(array $to): void
    {
        $this->removeTo();

        foreach ($to as $address) {
            $this->message->addTo($address);
        }
    }

    /**
     * Remove all "To" receivers.
     */
    protected function removeTo(): void
    {
        $this->message->to();
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
