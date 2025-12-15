<?php

namespace Sagautam5\EmailBlocker\Services;

use Sagautam5\EmailBlocker\Models\BlockedEmail;
use Sagautam5\EmailBlocker\Supports\BlockedEmailContext;

class EmailLogger
{
    public function info(BlockedEmailContext $blockedContext)
    {
        $message = $blockedContext->context->message;

        $data = [
            'email' => $blockedContext->email,
            'reason' => $blockedContext->reason,
            'subject' => $blockedContext->context->getSubject(),
            'from_name' => $blockedContext->context->getFromName(),
            'from_email' => $blockedContext->context->getFromEmail(),
            'content' => $this->extractContent($message),
            'rule' => $blockedContext->rule,
            'receiver_type' => $blockedContext->receiver_type
        ];

        BlockedEmail::create($data);
    }

    private function extractContent($message): ?string
    {
        if ($message === null) {
            return null;
        }

        return $message->getHtmlBody()
            ?? $message->getTextBody();
    }
}
