<?php 

namespace Sagautam5\LaravelEmailBlocker\Supports;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;

class EmailContext {
    public Mailable $mailable;
    public array $recipients;
    public Message $message;
}
