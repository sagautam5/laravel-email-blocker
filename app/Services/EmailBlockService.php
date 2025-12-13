<?php

namespace Sagautam5\EmailBlocker\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Pipeline\Pipeline;
use Sagautam5\EmailBlocker\Rules\BaseRule;
use Sagautam5\EmailBlocker\Supports\EmailContext;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailBlockService
{
    private EmailContext $context;

    public function __construct(protected Email $message, ?string $mailable = null)
    {
        $this->context = new EmailContext($this->message, $mailable);
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
        $emails = $this->context->getToEmails();

        $receivers = $this->getFilteredReceivers($emails);

        $this->applyTo($receivers);
    }

    /**
     * Apply Rules on "Cc" receivers.
     */
    protected function applyRulesOnCc(): void
    {
        $emails = $this->context->getCcEmails();

        $receivers = $this->getFilteredReceivers($emails);

        $this->applyCc($receivers);
    }

    /**
     * Apply Rules on "Bcc" receivers.
     */
    protected function applyRulesOnBcc(): void
    {
        $emails = $this->context->getBccEmails();
        
        $receivers = $this->getFilteredReceivers($emails);

        $this->applyBcc($receivers);
    }

    /**
     * Check if all receivers are empty.
     */
    protected function checkIfEmptyReceivers(): bool
    {
        $this->context = new EmailContext($this->message);

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
        $rules = $this->getBlockRules();

        return app(Pipeline::class)
            ->send($emails)
            ->through($rules)
            ->thenReturn();
    }

    protected function getBlockRules(): array
    {
        return array_map(function ($rule) {
            $instance = app($rule);

            if ($instance instanceof BaseRule) {
                $instance->setContext($this->context);
            }

            return $instance;
        }, config('email-blocker.rules'));
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
