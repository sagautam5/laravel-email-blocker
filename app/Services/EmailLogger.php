<?php

namespace Sagautam5\EmailBlocker\Services;

use Sagautam5\EmailBlocker\Models\BlockedEmail;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;
use Symfony\Component\Mime\Email;

class EmailLogger
{
    public function info(BlockedEmailContext $blockedContext)
    {
        $message = $blockedContext->context->message;

        $data = [
            'mailable' => $blockedContext->context->mailable ?? null,
            'email' => $blockedContext->email,
            'reason' => $blockedContext->reason,
            'subject' => $blockedContext->context->getSubject(),
            'from_name' => $blockedContext->context->getFromName(),
            'from_email' => $blockedContext->context->getFromEmail(),
            'content' => $this->extractContent($message),
            'rule' => $blockedContext->rule,
            'receiver_type' => $blockedContext->receiver_type,
        ];

        BlockedEmail::create($data);
    }

    private function extractContent(?Email $message): ?string
    {
        $content = $message?->getHtmlBody()
            ?? $message?->getTextBody();

        return mb_substr($content, 0, 10000);
    }
}
