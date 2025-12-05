<?php 

namespace Sagautam5\EmailBlocker\Supports;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;

class EmailContext {
    public Mailable $mailable;
    public array $recipients;
    public Message $message;
}
